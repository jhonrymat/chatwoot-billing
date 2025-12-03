<?php

namespace App\Filament\Resources\ChatwootAccounts\Pages;

use App\Filament\Resources\ChatwootAccounts\ChatwootAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditChatwootAccount extends EditRecord
{
    protected static string $resource = ChatwootAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
