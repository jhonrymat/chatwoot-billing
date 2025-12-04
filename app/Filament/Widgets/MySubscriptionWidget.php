<?php


namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MySubscriptionWidget extends Widget
{
    protected string $view = 'filament.widgets.my-subscription-widget';

    protected int | string | array $columnSpan = 'full';

    // Solo visible para subscribers
    public static function canView(): bool
    {
        return auth()->user()->isSubscriber();
    }

    public function getSubscription()
    {
        return auth()->user()->activeSubscription;
    }
}
