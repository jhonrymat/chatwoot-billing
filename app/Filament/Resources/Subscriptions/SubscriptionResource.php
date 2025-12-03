<?php

// ============================================
// app/Filament/Resources/Subscriptions/SubscriptionResource.php
// ============================================

namespace App\Filament\Resources\Subscriptions;

use Filament\Tables\Table;
use App\Models\Subscription;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Subscriptions\Pages\ListSubscriptions;
use App\Filament\Resources\Subscriptions\Pages\ViewSubscription;
use App\Filament\Resources\Subscriptions\Pages\EditSubscription;
use BackedEnum;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'Suscripciones';

    protected static string | \UnitEnum | null $navigationGroup = 'Facturación';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return Schemas\SubscriptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Tables\SubscriptionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscriptions::route('/'),
            'view' => ViewSubscription::route('/{record}'),
            'edit' => EditSubscription::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::getModel()::where('status', 'active');

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return $query->count();
    }

    public static function canViewAny(): bool
    {
        return true; // Todos pueden ver (filtrado en query)
    }

    public static function canCreate(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }
}
