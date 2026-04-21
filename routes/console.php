<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Runs every day at midnight:
// - Updates domain statuses in bulk (Expired / Expiring / Active)
// - Sends email alerts to branch managers & employees for domains
//   expiring in exactly 30, 15, or 7 days.
Schedule::command('app:notify-domain-expiry')->dailyAt('08:00');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
