<?php

namespace App\DTOs;

class PaymentRequest
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $description,
        public readonly string $userEmail,
        public readonly ?string $paymentMethodId = null,
        public readonly ?string $token = null,
    ) {}
}
