<?php

namespace App\Filament\Resources\UserWebhooks\Pages;

use App\Filament\Resources\UserWebhooks\UserWebhookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserWebhook extends CreateRecord
{
    protected static string $resource = UserWebhookResource::class;
}
