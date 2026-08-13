<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send streak break reminders daily at 6:00 PM
Schedule::command('app:send-streak-reminders')->dailyAt('18:00');

// Cleanup notifications older than 15 days, daily at midnight
Schedule::command('app:cleanup-notifications')->dailyAt('00:00');
