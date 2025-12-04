<?php
// ============================================
// app/Filament/Pages/MyBilling.php
// Página de facturación del suscriptor
// ============================================

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;

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
        return auth()->user()->hasRole('subscriber');
    }

    public function mount()
    {
        $this->subscription = auth()->user()->activeSubscription;
        $this->payments = auth()->user()->payments()
            ->with('subscription.plan')
            ->latest()
            ->limit(10)
            ->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadInvoices')
                ->label('Descargar Facturas')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(route('invoices.download', ['user' => auth()->id()]))
                ->visible(fn () => $this->payments->isNotEmpty()),
        ];
    }

    public function cancelSubscription()
    {
        if (!$this->subscription) {
            Notification::make()
                ->title('No hay suscripción activa')
                ->warning()
                ->send();
            return;
        }

        try {
            // Llamar al servicio de cancelación
            $this->subscription->update(['status' => 'cancelled']);

            // Registrar actividad
            activity()
                ->performedOn($this->subscription)
                ->causedBy(auth()->user())
                ->log('Suscripción cancelada por el usuario');

            Notification::make()
                ->title('Suscripción cancelada')
                ->body('Tu suscripción se cancelará al final del período actual')
                ->success()
                ->send();

            $this->redirect(route('filament.admin.pages.my-dashboard'));
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
