<?php
namespace App\Filament\Resources\Plans\Schemas;

use App\Enums\BillingCycle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información Básica')
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre del Plan')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('slug', \Illuminate\Support\Str::slug($state));
                        }),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->helperText('Se genera automáticamente del nombre'),

                    Textarea::make('description')
                        ->label('Descripción')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Precio')
                ->schema([
                    TextInput::make('price')
                        ->label('Precio')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->default(0)
                        ->step(100),

                    TextInput::make('currency')
                        ->label('Moneda')
                        ->required()
                        ->default('COP')
                        ->maxLength(3),

                    Select::make('billing_cycle')
                        ->label('Ciclo de Facturación')
                        ->required()
                        ->options(BillingCycle::class)
                        ->default('monthly'),
                ])->columns(3),

            Section::make('Límites del Plan')
                ->schema([
                    TextInput::make('max_agents')
                        ->label('Máximo de Agentes')
                        ->required()
                        ->numeric()
                        ->default(5)
                        ->minValue(1),

                    TextInput::make('max_inboxes')
                        ->label('Máximo de Inboxes')
                        ->required()
                        ->numeric()
                        ->default(3)
                        ->minValue(1),

                    TextInput::make('max_contacts')
                        ->label('Máximo de Contactos')
                        ->required()
                        ->numeric()
                        ->default(1000)
                        ->minValue(1),

                    TextInput::make('max_conversations_per_month')
                        ->label('Conversaciones por Mes')
                        ->numeric()
                        ->nullable()
                        ->helperText('Dejar vacío para ilimitado'),
                ])->columns(2),

            Section::make('Características')
                ->schema([
                    TagsInput::make('features')
                        ->label('Features del Plan')
                        ->placeholder('Presiona Enter después de cada feature')
                        ->helperText('Ejemplo: Soporte 24/7, API Access, etc.')
                        ->columnSpanFull(),
                ]),

            Section::make('Configuración')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Plan Activo')
                        ->default(true)
                        ->helperText('Los planes inactivos no se muestran a los usuarios'),

                    Toggle::make('is_popular')
                        ->label('Plan Popular')
                        ->default(false)
                        ->helperText('Se mostrará con badge de "Popular"'),

                    TextInput::make('sort_order')
                        ->label('Orden de Visualización')
                        ->numeric()
                        ->default(0)
                        ->helperText('Menor número = mayor prioridad'),

                    TextInput::make('gateway_plan_id')
                        ->label('Gateway Plan ID')
                        ->helperText('ID del plan en MercadoPago (opcional)')
                        ->maxLength(255),
                ])->columns(2),
        ]);
    }
}
