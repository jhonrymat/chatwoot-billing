<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentRequest;
use App\DTOs\PaymentResponse;
use App\DTOs\SubscriptionRequest;
use App\DTOs\SubscriptionResponse;
use App\Models\PaymentMethod;

class FakeGateway implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'fake';
    }

    public function initialize(array $credentials): void
    {
        // No hace nada
    }

    public function createSubscription(SubscriptionRequest $request): SubscriptionResponse
    {
        return new SubscriptionResponse(
            subscriptionId: 'fake_sub_' . uniqid(),
            status: 'active',
            checkoutUrl: 'https://fake-gateway.test/checkout',
            metadata: ['fake' => true]
        );
    }

    public function cancelSubscription(string $subscriptionId): bool
    {
        return true;
    }

    public function getSubscriptionStatus(string $subscriptionId): array
    {
        return [
            'id' => $subscriptionId,
            'status' => 'active',
            'raw_status' => 'active',
        ];
    }

    public function createPayment(PaymentRequest $request): PaymentResponse
    {
        return new PaymentResponse(
            paymentId: 'fake_pay_' . uniqid(),
            status: 'approved',
            amount: $request->amount,
            currency: $request->currency ?? 'COP',
            metadata: ['fake' => true]
        );
    }

    public function handleWebhook(array $payload): array
    {
        return [
            'event_type' => 'fake.event',
            'processed' => true,
        ];
    }

    public function validateWebhookSignature(array $payload, string $signature): bool
    {
        return true;
    }

    public function savePaymentMethod(array $data): PaymentMethod
    {
        return PaymentMethod::create([
            'user_id' => $data['user_id'],
            'payment_gateway' => 'fake',
            'type' => 'credit_card',
            'gateway_card_id' => 'fake_card_' . uniqid(),
            'gateway_customer_id' => 'fake_customer_' . uniqid(),
            'last_four_digits' => '4242',
            'card_brand' => 'visa',
            'expiration_month' => '12',
            'expiration_year' => '2030',
            'cardholder_name' => 'Test User',
            'is_default' => $data['is_default'] ?? false,
        ]);
    }

    public function getCheckoutUrl(string $subscriptionId): string
    {
        return 'https://fake-gateway.test/checkout/' . $subscriptionId;
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function getSupportedCountries(): array
    {
        return ['*']; // Soporta todos
    }
}
