<?php
class SubscriptionResponse
{
    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $status,
        public readonly ?string $checkoutUrl = null,
        public readonly ?array $metadata = null,
    ) {}
}
