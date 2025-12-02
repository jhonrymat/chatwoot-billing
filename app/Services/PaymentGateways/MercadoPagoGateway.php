<?php

// ============================================
// app/Services/PaymentGateways/MercadoPagoGateway.php
// ============================================

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentRequest;
use App\DTOs\PaymentResponse;
use App\DTOs\SubscriptionRequest;
use App\DTOs\SubscriptionResponse;
use App\Models\PaymentMethod;
use MercadoPago\SDK;
use MercadoPago\Subscription;
use MercadoPago\Payment;
use MercadoPago\Customer;
use MercadoPago\Card;

class MercadoPagoGateway extends AbstractPaymentGateway
{
    protected $sdk;

    public function getName(): string
    {
        return 'mercadopago';
    }

    public function initialize(array $credentials): void
    {
        SDK::setAccessToken($credentials['access_token'] ?? config('mercadopago.access_token'));
        $this->sdk = new SDK();
    }

    public function createSubscription(SubscriptionRequest $request): SubscriptionResponse
    {
        try {
            $subscription = new Subscription();

            $subscription->reason = $request->planName;
            $subscription->payer_email = $request->userEmail;
            $subscription->auto_recurring = [
                'frequency' => 1,
                'frequency_type' => $request->billingCycle === 'yearly' ? 'months' : 'months',
                'transaction_amount' => $request->amount,
                'currency_id' => $request->currency ?? 'COP',
            ];

            $subscription->back_url = config('mercadopago.urls.success');

            if ($request->paymentMethodId) {
                $subscription->payment_method_id = $request->paymentMethodId;
            }

            $subscription->save();

            return new SubscriptionResponse(
                subscriptionId: $subscription->id,
                status: $this->mapSubscriptionStatus($subscription->status),
                checkoutUrl: $subscription->init_point,
                metadata: [
                    'preapproval_id' => $subscription->preapproval_plan_id ?? null,
                    'raw_status' => $subscription->status,
                ]
            );

        } catch (\Exception $e) {
            throw new \RuntimeException("Error creating MercadoPago subscription: {$e->getMessage()}");
        }
    }

    public function cancelSubscription(string $subscriptionId): bool
    {
        try {
            $subscription = Subscription::find_by_id($subscriptionId);
            $subscription->status = 'cancelled';
            return $subscription->update();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error cancelling subscription: {$e->getMessage()}");
        }
    }

    public function getSubscriptionStatus(string $subscriptionId): array
    {
        try {
            $subscription = Subscription::find_by_id($subscriptionId);

            return [
                'id' => $subscription->id,
                'status' => $this->mapSubscriptionStatus($subscription->status),
                'raw_status' => $subscription->status,
                'next_payment_date' => $subscription->next_payment_date ?? null,
                'payer_id' => $subscription->payer_id ?? null,
            ];
        } catch (\Exception $e) {
            throw new \RuntimeException("Error getting subscription status: {$e->getMessage()}");
        }
    }

    public function createPayment(PaymentRequest $request): PaymentResponse
    {
        try {
            $payment = new Payment();

            $payment->transaction_amount = $request->amount;
            $payment->description = $request->description;
            $payment->payment_method_id = $request->paymentMethodId;
            $payment->payer = [
                'email' => $request->userEmail,
            ];

            if ($request->token) {
                $payment->token = $request->token;
            }

            $payment->save();

            return new PaymentResponse(
                paymentId: (string) $payment->id,
                status: $this->mapPaymentStatus($payment->status),
                amount: $payment->transaction_amount,
                currency: $payment->currency_id,
                metadata: [
                    'status_detail' => $payment->status_detail,
                    'payment_type' => $payment->payment_type_id,
                ]
            );

        } catch (\Exception $e) {
            throw new \RuntimeException("Error creating payment: {$e->getMessage()}");
        }
    }

    public function handleWebhook(array $payload): array
    {
        $type = $payload['type'] ?? null;
        $action = $payload['action'] ?? null;
        $data = $payload['data'] ?? [];

        $result = [
            'event_type' => $type,
            'action' => $action,
            'resource_id' => $data['id'] ?? null,
            'processed' => false,
        ];

        try {
            switch ($type) {
                case 'payment':
                    $payment = Payment::find_by_id($data['id']);
                    $result['status'] = $this->mapPaymentStatus($payment->status);
                    $result['processed'] = true;
                    break;

                case 'subscription':
                case 'preapproval':
                    $subscription = Subscription::find_by_id($data['id']);
                    $result['status'] = $this->mapSubscriptionStatus($subscription->status);
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
        // MercadoPago usa x-signature header
        // Por ahora retornamos true, implementar validación según docs de MP
        $secret = config('mercadopago.webhook_secret');

        if (!$secret) {
            return true; // En desarrollo sin secret
        }

        // Implementar validación real según documentación de MercadoPago
        // https://www.mercadopago.com.co/developers/es/docs/your-integrations/notifications/webhooks

        return true;
    }

    public function savePaymentMethod(array $data): PaymentMethod
    {
        try {
            // Crear o actualizar customer en MercadoPago
            $customer = new Customer();
            $customer->email = $data['email'];
            $customer->save();

            // Guardar tarjeta
            $card = new Card();
            $card->token = $data['token'];
            $card->customer_id = $customer->id;
            $card->save();

            return PaymentMethod::create([
                'user_id' => $data['user_id'],
                'payment_gateway' => 'mercadopago',
                'type' => $this->mapCardType($card->payment_method->payment_type_id),
                'gateway_card_id' => $card->id,
                'gateway_customer_id' => $customer->id,
                'last_four_digits' => $card->last_four_digits,
                'card_brand' => $card->payment_method->id,
                'expiration_month' => $card->expiration_month,
                'expiration_year' => $card->expiration_year,
                'cardholder_name' => $card->cardholder->name,
                'is_default' => $data['is_default'] ?? false,
            ]);

        } catch (\Exception $e) {
            throw new \RuntimeException("Error saving payment method: {$e->getMessage()}");
        }
    }

    public function getCheckoutUrl(string $subscriptionId): string
    {
        try {
            $subscription = Subscription::find_by_id($subscriptionId);
            return $subscription->init_point;
        } catch (\Exception $e) {
            throw new \RuntimeException("Error getting checkout URL: {$e->getMessage()}");
        }
    }

    public function getSupportedCountries(): array
    {
        return ['AR', 'BR', 'CL', 'CO', 'MX', 'PE', 'UY'];
    }

    // Helper methods
    protected function mapSubscriptionStatus(string $status): string
    {
        return match ($status) {
            'authorized', 'active' => 'active',
            'pending' => 'pending',
            'paused' => 'suspended',
            'cancelled' => 'cancelled',
            default => 'pending',
        };
    }

    protected function mapPaymentStatus(string $status): string
    {
        return match ($status) {
            'approved' => 'approved',
            'pending', 'in_process' => 'pending',
            'rejected' => 'rejected',
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
            'charged_back' => 'charged_back',
            default => 'pending',
        };
    }

    protected function mapCardType(string $type): string
    {
        return match ($type) {
            'credit_card' => 'credit_card',
            'debit_card' => 'debit_card',
            'pse' => 'pse',
            default => 'other',
        };
    }
}
