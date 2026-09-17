<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('model:prune')->daily();
Schedule::command('api-logs:aggregate')->dailyAt('00:05');
Schedule::command('storagebox:cache-archives')->everyThirtyMinutes();

