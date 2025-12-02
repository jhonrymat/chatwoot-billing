<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentRequest;
use App\DTOs\PaymentResponse;
use App\DTOs\SubscriptionRequest;
use App\DTOs\SubscriptionResponse;
use App\Models\PaymentMethod;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Stripe\Webhook;

class StripeGateway extends AbstractPaymentGateway
{
    protected StripeClient $client;

    public function getName(): string
    {
        return 'stripe';
    }

    public function initialize(array $credentials): void
    {
        $this->client = new StripeClient(
            $credentials['secret_key'] ?? config('stripe.secret_key')
        );
    }

    public function createSubscription(SubscriptionRequest $request): SubscriptionResponse
    {
        try {
            // Crear o recuperar customer
            $customer = $this->getOrCreateCustomer($request->userEmail, $request->userName);

            // Crear subscription
            $subscription = $this->client->subscriptions->create([
                'customer' => $customer->id,
                'items' => [
                    ['price' => $request->stripePriceId],
                ],
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => [
                    'save_default_payment_method' => 'on_subscription'
                ],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            $clientSecret = $subscription->latest_invoice->payment_intent->client_secret;

            return new SubscriptionResponse(
                subscriptionId: $subscription->id,
                status: $this->mapSubscriptionStatus($subscription->status),
                checkoutUrl: null, // Stripe usa client secret para frontend
                metadata: [
                    'customer_id' => $customer->id,
                    'client_secret' => $clientSecret,
                    'raw_status' => $subscription->status,
                ]
            );

        } catch (ApiErrorException $e) {
            throw new \RuntimeException("Error creating Stripe subscription: {$e->getMessage()}");
        }
    }

    public function cancelSubscription(string $subscriptionId): bool
    {
        try {
            $subscription = $this->client->subscriptions->cancel($subscriptionId);
            return $subscription->status === 'canceled';
        } catch (ApiErrorException $e) {
            throw new \RuntimeException("Error cancelling subscription: {$e->getMessage()}");
        }
    }

    public function getSubscriptionStatus(string $subscriptionId): array
    {
        try {
            $subscription = $this->client->subscriptions->retrieve($subscriptionId);

            return [
                'id' => $subscription->id,
                'status' => $this->mapSubscriptionStatus($subscription->status),
                'raw_status' => $subscription->status,
                'current_period_end' => $subscription->current_period_end,
                'customer_id' => $subscription->customer,
            ];
        } catch (ApiErrorException $e) {
            throw new \RuntimeException("Error getting subscription status: {$e->getMessage()}");
        }
    }

    public function createPayment(PaymentRequest $request): PaymentResponse
    {
        try {
            $paymentIntent = $this->client->paymentIntents->create([
                'amount' => $request->amount * 100, // Stripe usa centavos
                'currency' => strtolower($request->currency ?? 'usd'),
                'description' => $request->description,
                'payment_method' => $request->paymentMethodId,
                'confirm' => true,
                'return_url' => config('stripe.return_url'),
            ]);

            return new PaymentResponse(
                paymentId: $paymentIntent->id,
                status: $this->mapPaymentStatus($paymentIntent->status),
                amount: $paymentIntent->amount / 100,
                currency: strtoupper($paymentIntent->currency),
                metadata: [
                    'client_secret' => $paymentIntent->client_secret,
                ]
            );

        } catch (ApiErrorException $e) {
            throw new \RuntimeException("Error creating payment: {$e->getMessage()}");
        }
    }

    public function handleWebhook(array $payload): array
    {
        $event = $payload;
        $type = $event['type'] ?? null;
        $data = $event['data']['object'] ?? [];

        $result = [
            'event_type' => $type,
            'resource_id' => $data['id'] ?? null,
            'processed' => false,
        ];

        try {
            switch ($type) {
                case 'payment_intent.succeeded':
                case 'payment_intent.payment_failed':
                    $result['status'] = $this->mapPaymentStatus($data['status']);
                    $result['processed'] = true;
                    break;

                case 'customer.subscription.created':
                case 'customer.subscription.updated':
                case 'customer.subscription.deleted':
                    $result['status'] = $this->mapSubscriptionStatus($data['status']);
                    $result['processed'] = true;
                    break;

                case 'invoice.paid':
                case 'invoice.payment_failed':
                    $result['status'] = $data['status'];
                    $result['processed'] = true;
                    break;
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    public function validateWebhookSignature(array $payload, string $signature): bool
    {
        try {
            $webhookSecret = config('stripe.webhook_secret');

            if (!$webhookSecret) {
                return true; // En desarrollo
            }

            Webhook::constructEvent(
                json_encode($payload),
                $signature,
                $webhookSecret
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function savePaymentMethod(array $data): PaymentMethod
    {
        try {
            $customer = $this->getOrCreateCustomer($data['email'], $data['name']);

            // Adjuntar método de pago al customer
            $paymentMethod = $this->client->paymentMethods->attach(
                $data['payment_method_id'],
                ['customer' => $customer->id]
            );

            // Obtener detalles de la tarjeta
            $card = $paymentMethod->card;

            return PaymentMethod::create([
                'user_id' => $data['user_id'],
                'payment_gateway' => 'stripe',
                'type' => $card->funding === 'credit' ? 'credit_card' : 'debit_card',
                'gateway_card_id' => $paymentMethod->id,
                'gateway_customer_id' => $customer->id,
                'last_four_digits' => $card->last4,
                'card_brand' => $card->brand,
                'expiration_month' => str_pad($card->exp_month, 2, '0', STR_PAD_LEFT),
                'expiration_year' => (string) $card->exp_year,
                'cardholder_name' => $paymentMethod->billing_details->name,
                'is_default' => $data['is_default'] ?? false,
            ]);

        } catch (ApiErrorException $e) {
            throw new \RuntimeException("Error saving payment method: {$e->getMessage()}");
        }
    }

    public function getCheckoutUrl(string $subscriptionId): string
    {
        // Stripe no usa URLs de checkout para suscripciones
        // Se maneja con client secret en el frontend
        return '';
    }

    public function getSupportedCountries(): array
    {
        return [
            'US', 'CA', 'GB', 'AU', 'NZ', 'DE', 'FR', 'ES', 'IT', 'NL',
            'BE', 'CH', 'AT', 'IE', 'DK', 'FI', 'NO', 'SE', 'PT', 'PL'
        ];
    }

    // Helper methods
    protected function getOrCreateCustomer(string $email, ?string $name = null): \Stripe\Customer
    {
        try {
            // Buscar customer existente
            $customers = $this->client->customers->search([
                'query' => "email:'{$email}'",
                'limit' => 1,
            ]);

            if (count($customers->data) > 0) {
                return $customers->data[0];
            }

            // Crear nuevo customer
            return $this->client->customers->create([
                'email' => $email,
                'name' => $name,
            ]);

        } catch (ApiErrorException $e) {
            throw new \RuntimeException("Error managing customer: {$e->getMessage()}");
        }
    }

    protected function mapSubscriptionStatus(string $status): string
    {
        return match ($status) {
            'active', 'trialing' => 'active',
            'incomplete', 'incomplete_expired' => 'pending',
            'past_due' => 'suspended',
            'canceled', 'unpaid' => 'cancelled',
            default => 'pending',
        };
    }

    protected function mapPaymentStatus(string $status): string
    {
        return match ($status) {
            'succeeded' => 'approved',
            'processing', 'requires_action', 'requires_confirmation' => 'pending',
            'requires_payment_method', 'canceled' => 'rejected',
            default => 'pending',
        };
    }
}
