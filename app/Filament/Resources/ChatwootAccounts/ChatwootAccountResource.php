<?php

namespace App\Filament\Resources\ChatwootAccounts;

use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\ChatwootAccount;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ChatwootAccounts\Pages\EditChatwootAccount;
use App\Filament\Resources\ChatwootAccounts\Pages\ViewChatwootAccount;
use App\Filament\Resources\ChatwootAccounts\Pages\ListChatwootAccounts;
use App\Filament\Resources\ChatwootAccounts\Pages\CreateChatwootAccount;
use App\Filament\Resources\ChatwootAccounts\Schemas\ChatwootAccountForm;
use App\Filament\Resources\ChatwootAccounts\Tables\ChatwootAccountsTable;

class ChatwootAccountResource extends Resource
{
    protected static ?string $model = ChatwootAccount::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $recordTitleAttribute = 'ChatwootAccount';

     protected static ?string $navigationLabel = 'Cuentas Chatwoot';

    protected static string | \UnitEnum | null $navigationGroup = 'Chatwoot';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ChatwootAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChatwootAccountsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChatwootAccounts::route('/'),
            'view' => ViewChatwootAccount::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::getModel()::where('status', 'active');

        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return $query->count();
    }
}
