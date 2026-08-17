<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Keeps the next 14 days topped up with a quote even if no admin visits the
// schedule page — admin-made assignments are never touched by this.
Schedule::command('quotes:assign-schedules')->daily();

// Temporary diagnostic: confirms the scheduler is actually running on prod.
// Remove once verified.
// Schedule::command('app:scheduler-heartbeat')->everyMinute();
