<?php

namespace App\Filament\Resources\UserWebhooks\Tables;

use Filament\Tables\Table;
use App\Enums\WebhookEvent;
use App\Models\UserWebhook;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use App\Services\WebhookDispatcher;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class UserWebhooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('event')
                    ->badge()
                    ->formatStateUsing(fn($state) => WebhookEvent::from($state)->label()),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Activo'),
                TextColumn::make('success_count')
                    ->label('Éxitos')
                    ->sortable(),
                TextColumn::make('failure_count')
                    ->label('Fallos')
                    ->sortable(),
                TextColumn::make('last_triggered_at')
                    ->label('Último Disparo')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->options(WebhookEvent::class),
                TernaryFilter::make('is_active'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('test')
                    ->icon('heroicon-o-play')
                    ->action(function (UserWebhook $record) {
                        WebhookDispatcher::dispatch(
                            $record->event,
                            $record->user,
                            ['test' => true, 'triggered_at' => now()]
                        );
                        Notification::make()
                            ->title('Webhook de prueba enviado')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
