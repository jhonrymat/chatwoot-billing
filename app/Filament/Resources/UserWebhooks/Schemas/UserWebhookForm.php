<?php

namespace App\Filament\Resources\UserWebhooks\Schemas;

use App\Enums\WebhookEvent;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;

class UserWebhookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Nombre del Webhook')
                    ->placeholder('Ej: Notificar en WhatsApp'),

                TextInput::make('url')
                    ->required()
                    ->url()
                    ->label('URL del Webhook')
                    ->placeholder('https://n8n.io/webhook/abc123')
                    ->helperText('Esta URL será llamada cuando ocurra el evento'),

                Select::make('event')
                    ->required()
                    ->options(WebhookEvent::class)
                    ->label('Evento')
                    ->helperText('¿Cuándo debe dispararse este webhook?'),

                TextInput::make('secret')
                    ->label('Secret (Opcional)')
                    ->password()
                    ->helperText('Para verificar la firma del webhook')
                    ->revealable(),

                KeyValue::make('headers')
                    ->label('Headers Personalizados')
                    ->helperText('Ej: Authorization, X-API-Key')
                    ->reorderable(),

                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),

                Grid::make(2)->schema([
                    TextInput::make('max_retries')
                        ->numeric()
                        ->default(3)
                        ->label('Máximo de Reintentos'),

                    TextInput::make('timeout')
                        ->numeric()
                        ->default(10)
                        ->suffix('segundos')
                        ->label('Timeout'),
                ])
            ]);
    }
}
