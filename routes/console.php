<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('chatwoot:sync-metrics')
    ->hourly()
    ->withoutOverlapping();

Schedule::command('chatwoot:check-limits')
    ->daily()
    ->at('09:00');

// Procesar renovaciones diariamente
Schedule::command('subscriptions:process-renewals')
    ->daily()
    ->at('00:00');

// Enviar recordatorios de renovación
Schedule::command('subscriptions:send-reminders')
    ->daily()
    ->at('10:00');
