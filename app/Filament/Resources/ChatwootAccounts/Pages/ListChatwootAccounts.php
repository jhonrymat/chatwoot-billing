<?php

namespace App\Filament\Resources\ChatwootAccounts\Pages;

use App\Filament\Resources\ChatwootAccounts\ChatwootAccountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChatwootAccounts extends ListRecords
{
    protected static string $resource = ChatwootAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
