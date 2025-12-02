<?php

namespace App\Filament\Resources\UserWebhooks;

use App\Filament\Resources\UserWebhooks\Pages\CreateUserWebhook;
use App\Filament\Resources\UserWebhooks\Pages\EditUserWebhook;
use App\Filament\Resources\UserWebhooks\Pages\ListUserWebhooks;
use App\Filament\Resources\UserWebhooks\Schemas\UserWebhookForm;
use App\Filament\Resources\UserWebhooks\Tables\UserWebhooksTable;
use App\Models\UserWebhook;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserWebhookResource extends Resource
{
    protected static ?string $model = UserWebhook::class;
    // icono de webhooks
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static ?string $recordTitleAttribute = 'name';


    public static function form(Schema $schema): Schema
    {
        return UserWebhookForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserWebhooksTable::configure($table);
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
            'index' => ListUserWebhooks::route('/'),
            'create' => CreateUserWebhook::route('/create'),
            'edit' => EditUserWebhook::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Si no es admin, solo ver sus propios webhooks
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }
}
