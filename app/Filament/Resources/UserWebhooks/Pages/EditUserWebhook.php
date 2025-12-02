<?php

namespace App\Filament\Resources\UserWebhooks\Pages;

use App\Filament\Resources\UserWebhooks\UserWebhookResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserWebhook extends EditRecord
{
    protected static string $resource = UserWebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
