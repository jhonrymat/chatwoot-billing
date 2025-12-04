<?php

namespace App\Livewire;

use App\Models\Plan;
use App\Services\MercadoPagoService;
use Livewire\Component;
use Filament\Notifications\Notification;

class SubscriptionUpgrade extends Component
{
    public $currentPlan;
    public $availablePlans;
    public $selectedPlan;

    public function mount()
    {
        $subscription = auth()->user()->activeSubscription;
        $this->currentPlan = $subscription?->plan;

        $this->availablePlans = Plan::active()
            ->where('id', '!=', $this->currentPlan?->id)
            ->ordered()
            ->get();
    }

    public function selectPlan($planId)
    {
        $this->selectedPlan = Plan::find($planId);
    }

    public function upgrade()
    {
        if (!$this->selectedPlan) {
            Notification::make()
                ->title('Selecciona un plan')
                ->warning()
                ->send();
            return;
        }

        try {
            $mercadoPago = app(MercadoPagoService::class);
            $preference = $mercadoPago->createSubscriptionPreference(
                auth()->user(),
                $this->selectedPlan
            );

            // Redirigir a MercadoPago
            return redirect()->away($preference['init_point']);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al procesar')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.subscription-upgrade');
    }
}
