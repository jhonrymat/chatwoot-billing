<?php

namespace App\Filament\Resources\ChatwootAccounts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ChatwootAccountForm
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
                TextInput::make('chatwoot_account_id')
                    ->required()
                    ->numeric(),
                TextInput::make('chatwoot_account_name')
                    ->required(),
                TextInput::make('chatwoot_user_id')
                    ->numeric(),
                TextInput::make('chatwoot_url')
                    ->url(),
                TextInput::make('chatwoot_dashboard_url')
                    ->url(),
                Select::make('status')
                    ->options(['active' => 'Active', 'suspended' => 'Suspended', 'deleted' => 'Deleted'])
                    ->default('active')
                    ->required(),
                TextInput::make('locale')
                    ->required()
                    ->default('es'),
                TextInput::make('timezone')
                    ->required()
                    ->default('America/Bogota'),
                DateTimePicker::make('last_synced_at'),
                Textarea::make('sync_error')
                    ->columnSpanFull(),
            ]);
    }
}
