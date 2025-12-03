<?php

namespace App\DTOs;

class SubscriptionRequest
{
    public function __construct(
        public readonly int $userId,
        public readonly string $userName,
        public readonly string $userEmail,
        public readonly int $planId,
        public readonly string $planName,
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $billingCycle,
        public readonly ?string $paymentMethodId = null,
    ) {}
}
