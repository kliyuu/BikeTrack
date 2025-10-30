<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule stock level checks every day
Schedule::command('stock:check')->daily();

// Auto-complete delivered orders after 24 hours - runs every hour
Schedule::command('orders:auto-complete')->hourly();
