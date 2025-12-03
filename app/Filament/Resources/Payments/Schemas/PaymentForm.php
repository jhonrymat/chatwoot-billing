<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('subscription_id')
                    ->relationship('subscription', 'id')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('payment_gateway')
                    ->required()
                    ->default('mercadopago'),
                Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'id'),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('COP'),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
            'charged_back' => 'Charged back',
        ])
                    ->required(),
                TextInput::make('gateway_payment_id')
                    ->required(),
                TextInput::make('gateway_status'),
                TextInput::make('gateway_status_detail'),
                TextInput::make('payment_method_id_gateway'),
                TextInput::make('payment_type'),
                TextInput::make('metadata'),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
