<?php

namespace App\DTOs;

class PaymentResponse
{
    public function __construct(
        public readonly string $paymentId,
        public readonly string $status,
        public readonly float $amount,
        public readonly string $currency,
        public readonly ?array $metadata = null,
    ) {}
}
