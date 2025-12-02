<?php
namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGatewayConfig;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    protected array $config;
    protected PaymentGatewayConfig $gatewayConfig;

    public function __construct()
    {
        $this->loadConfiguration();
    }

    protected function loadConfiguration(): void
    {
        $this->gatewayConfig = PaymentGatewayConfig::where('gateway_name', $this->getName())
            ->where('is_enabled', true)
            ->firstOrFail();

        $this->config = $this->gatewayConfig->credentials;
        $this->initialize($this->config);
    }

    public function isConfigured(): bool
    {
        return !empty($this->config) && $this->gatewayConfig->is_enabled;
    }

    abstract public function getName(): string;
}
