<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('metrics:sync-openrouter')->hourly();
Schedule::command('metrics:sync-vapi')->hourly();
Schedule::command('metrics:sync-n8n')->hourly();
