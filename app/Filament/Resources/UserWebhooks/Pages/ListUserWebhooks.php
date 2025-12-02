<?php

namespace App\Filament\Resources\UserWebhooks\Pages;

use App\Filament\Resources\UserWebhooks\UserWebhookResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserWebhooks extends ListRecords
{
    protected static string $resource = UserWebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
