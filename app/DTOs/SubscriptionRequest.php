<?php

class SubscriptionRequest
{
    public function __construct(
        public readonly int $userId,
        public readonly int $planId,
        public readonly ?string $paymentMethodId = null,
        public readonly ?array $metadata = null,
    ) {}
}
