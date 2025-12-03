<?php

namespace App\Filament\Resources\UserWebhooks\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserWebhookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('url')
                    ->url()
                    ->required(),
                Select::make('event')
                    ->options([
            'account.created' => 'Account.created',
            'account.suspended' => 'Account.suspended',
            'account.activated' => 'Account.activated',
            'payment.approved' => 'Payment.approved',
            'payment.failed' => 'Payment.failed',
            'subscription.activated' => 'Subscription.activated',
            'subscription.cancelled' => 'Subscription.cancelled',
            'subscription.renewed' => 'Subscription.renewed',
            'plan.limit_exceeded' => 'Plan.limit exceeded',
        ])
                    ->required(),
                TextInput::make('secret'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('headers'),
                TextInput::make('max_retries')
                    ->required()
                    ->numeric()
                    ->default(3),
                TextInput::make('timeout')
                    ->required()
                    ->numeric()
                    ->default(10),
                TextInput::make('success_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('failure_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('last_triggered_at'),
            ]);
    }
}
