<?php

namespace App\Contracts;

use App\DTOs\PaymentRequest;
use App\DTOs\PaymentResponse;
use App\DTOs\SubscriptionRequest;
use App\DTOs\SubscriptionResponse;
use App\Models\PaymentMethod;

interface PaymentGatewayInterface
{
    /**
     * Nombre del gateway
     */
    public function getName(): string;

    /**
     * Inicializar el cliente del gateway
     */
    public function initialize(array $credentials): void;

    /**
     * Crear una suscripción
     */
    public function createSubscription(SubscriptionRequest $request): SubscriptionResponse;

    /**
     * Cancelar una suscripción
     */
    public function cancelSubscription(string $subscriptionId): bool;

    /**
     * Obtener estado de una suscripción
     */
    public function getSubscriptionStatus(string $subscriptionId): array;

    /**
     * Crear un pago único
     */
    public function createPayment(PaymentRequest $request): PaymentResponse;

    /**
     * Procesar webhook
     */
    public function handleWebhook(array $payload): array;

    /**
     * Validar firma de webhook
     */
    public function validateWebhookSignature(array $payload, string $signature): bool;

    /**
     * Guardar método de pago
     */
    public function savePaymentMethod(array $data): PaymentMethod;

    /**
     * Obtener URL de checkout
     */
    public function getCheckoutUrl(string $subscriptionId): string;

    /**
     * Verificar si está configurado correctamente
     */
    public function isConfigured(): bool;

    /**
     * Países soportados
     */
    public function getSupportedCountries(): array;
}
