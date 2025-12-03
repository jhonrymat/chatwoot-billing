<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class WebhookDocumentation extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    protected static ?string $navigationLabel = 'Documentación Webhooks';

    public static function getNavigationGroup(): ?string
    {
        return 'Webhooks';
    }
    protected string $view = 'filament.pages.webhook-documentation';
}
