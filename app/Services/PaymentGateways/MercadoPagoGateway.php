<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentRequest;
use App\DTOs\PaymentResponse;
use App\DTOs\SubscriptionRequest;
use App\DTOs\SubscriptionResponse;
use App\Models\PaymentMethod;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Customer\CustomerClient;
use MercadoPago\Exceptions\MPApiException;

class MercadoPagoGateway extends AbstractPaymentGateway
{
    protected PaymentClient $paymentClient;
    protected PreferenceClient $preferenceClient;
    protected CustomerClient $customerClient;

    public function getName(): string
    {
        return 'mercadopago';
    }

    public function initialize(array $credentials): void
    {
        MercadoPagoConfig::setAccessToken($credentials['access_token']);

        $this->paymentClient = new PaymentClient();
        $this->preferenceClient = new PreferenceClient();
        $this->customerClient = new CustomerClient();
    }

    public function createSubscription(SubscriptionRequest $request): SubscriptionResponse
    {
        try {
            $preference = $this->preferenceClient->create([
                'items' => [[
                    'id' => (string) $request->planId,
                    'title' => $request->planName,
                    'quantity' => 1,
                    'unit_price' => $request->amount,
                    'currency_id' => $request->currency,
                ]],
                'payer' => [
                    'name' => $request->userName,
                    'email' => $request->userEmail,
                ],
                'back_urls' => [
                    'success' => route('payment.success'),
                    'failure' => route('payment.failure'),
                    'pending' => route('payment.pending'),
                ],
                'auto_return' => 'approved',
                'external_reference' => "subscription_{$request->userId}_{$request->planId}",
                'notification_url' => route('webhook.mercadopago'),
            ]);

            return new SubscriptionResponse(
                subscriptionId: $preference->id,
                status: 'pending',
                checkoutUrl: $preference->init_point,
                metadata: ['preference_id' => $preference->id]
            );

        } catch (MPApiException $e) {
            throw new \RuntimeException("MercadoPago error: {$e->getMessage()}");
        }
    }

    public function cancelSubscription(string $subscriptionId): bool
    {
        // MercadoPago no tiene cancelación directa de preferencias
        // Se maneja desde el dashboard o vía API de suscripciones recurrentes
        return true;
    }

    public function getSubscriptionStatus(string $subscriptionId): array
    {
        // No hay endpoint directo en SDK 3.x para esto
        return ['id' => $subscriptionId, 'status' => 'active'];
    }

    public function createPayment(PaymentRequest $request): PaymentResponse
    {
        try {
            $payment = $this->paymentClient->create([
                'transaction_amount' => $request->amount,
                'description' => $request->description,
                'payment_method_id' => $request->paymentMethodId,
                'payer' => [
                    'email' => $request->userEmail,
                ],
            ]);

            return new PaymentResponse(
                paymentId: (string) $payment->id,
                status: $this->mapPaymentStatus($payment->status),
                amount: $payment->transaction_amount,
                currency: $payment->currency_id,
                metadata: ['mp_status' => $payment->status]
            );

        } catch (MPApiException $e) {
            throw new \RuntimeException("Payment failed: {$e->getMessage()}");
        }
    }

    public function handleWebhook(array $payload): array
    {
        $type = $payload['type'] ?? null;
        $dataId = $payload['data']['id'] ?? null;

        return [
            'event_type' => $type,
            'resource_id' => $dataId,
            'processed' => true,
        ];
    }

    public function validateWebhookSignature(array $payload, string $signature): bool
    {
        // Implementar validación si es necesario
        return true;
    }

    public function savePaymentMethod(array $data): PaymentMethod
    {
        try {
            $customer = $this->customerClient->create([
                'email' => $data['email'],
                'first_name' => $data['name'] ?? '',
            ]);

            return PaymentMethod::create([
                'user_id' => $data['user_id'],
                'payment_gateway' => 'mercadopago',
                'type' => 'credit_card',
                'gateway_customer_id' => $customer->id,
                'is_default' => $data['is_default'] ?? false,
            ]);

        } catch (MPApiException $e) {
            throw new \RuntimeException("Failed to save payment method: {$e->getMessage()}");
        }
    }

    public function getCheckoutUrl(string $subscriptionId): string
    {
        return "https://www.mercadopago.com/checkout/v1/redirect?pref_id={$subscriptionId}";
    }

    public function getSupportedCountries(): array
    {
        return ['AR', 'BR', 'CL', 'CO', 'MX', 'PE', 'UY'];
    }

    protected function mapPaymentStatus(string $status): string
    {
        return match($status) {
            'approved' => 'approved',
            'pending', 'in_process' => 'pending',
            'rejected' => 'rejected',
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
            'charged_back' => 'charged_back',
            default => 'pending',
        };
    }
}
