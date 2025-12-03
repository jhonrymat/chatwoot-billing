<?php
namespace App\Filament\Resources\Plans\Tables;

use App\Models\Plan;
use Filament\Tables\Table;
use App\Enums\BillingCycle;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class PlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Precio')
                    ->money('COP')
                    ->sortable(),

                TextColumn::make('billing_cycle')
                    ->label('Ciclo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => BillingCycle::from($state)->label()),

                TextColumn::make('max_agents')
                    ->label('Agentes')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('max_inboxes')
                    ->label('Inboxes')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('subscriptions_count')
                    ->label('Suscripciones')
                    ->counts('subscriptions')
                    ->alignCenter()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_popular')
                    ->label('Popular')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Activo')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),

                TernaryFilter::make('is_popular')
                    ->label('Popular'),

                SelectFilter::make('billing_cycle')
                    ->label('Ciclo')
                    ->options(BillingCycle::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),

                Action::make('toggle_active')
                    ->label('Activar/Desactivar')
                    ->icon('heroicon-o-power')
                    ->action(fn (Plan $record) =>
                        $record->update(['is_active' => !$record->is_active])
                    )
                    ->requiresConfirmation()
                    ->color('warning'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('activate')
                        ->label('Activar seleccionados')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('deactivate')
                        ->label('Desactivar seleccionados')
                        ->icon('heroicon-o-x-mark')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->poll('30s');
    }
}
