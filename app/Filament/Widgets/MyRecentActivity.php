<?php

// ============================================
// app/Filament/Widgets/MyRecentActivity.php
// php artisan make:filament-widget MyRecentActivity --table
// ============================================

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MyRecentActivity extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->isSubscriber();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ActivityLog::query()
                    ->where('user_id', auth()->id())
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('action')
                    ->label('Acción')
                    ->badge()
                    ->formatStateUsing(fn ($state) => \App\Enums\ActivityAction::from($state)->label()),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->heading('Actividad Reciente');
    }
}
