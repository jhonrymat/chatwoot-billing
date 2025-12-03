<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->required(),
                TextInput::make('payment_gateway')
                    ->required()
                    ->default('mercadopago'),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'active' => 'Active',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
            'suspended' => 'Suspended',
        ])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('ends_at'),
                DateTimePicker::make('cancelled_at'),
                DateTimePicker::make('trial_ends_at'),
                TextInput::make('gateway_subscription_id'),
                TextInput::make('gateway_customer_id'),
                TextInput::make('mgateway_preapproval_id'),
                DatePicker::make('next_billing_date'),
                DatePicker::make('last_payment_date'),
            ]);
    }
}
