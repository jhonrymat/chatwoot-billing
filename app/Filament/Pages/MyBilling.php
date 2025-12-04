<?php
// ============================================
// app/Filament/Pages/MyBilling.php
// Página de facturación del suscriptor
// ============================================

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class MyBilling extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected string $view = 'filament.pages.my-billing';

    protected static ?string $navigationLabel = 'Mi Facturación';

    protected static string | \UnitEnum | null $navigationGroup = 'Mi Cuenta';

    protected static ?int $navigationSort = 2;

    public $subscription;
    public $payments;

    public static function canAccess(): bool
    {
        return auth()->user()->isSubscriber();
    }

    public function mount()
    {
        $this->subscription = auth()->user()->activeSubscription;
        $this->payments = auth()->user()->payments()
            ->latest()
            ->limit(10)
            ->get();
    }

    public function cancelSubscription()
    {
        if (!$this->subscription) {
            \Filament\Notifications\Notification::make()
                ->title('No hay suscripción activa')
                ->warning()
                ->send();
            return;
        }

        try {
            $this->subscription->cancel();

            \Filament\Notifications\Notification::make()
                ->title('Suscripción cancelada')
                ->body('Tu suscripción ha sido cancelada exitosamente')
                ->success()
                ->send();

            $this->redirect(route('filament.admin.pages.my-dashboard'));
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
