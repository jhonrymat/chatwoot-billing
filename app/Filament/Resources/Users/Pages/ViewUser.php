<?php

namespace App\Filament\Resources\Users\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Payments\PaymentResource;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

}
