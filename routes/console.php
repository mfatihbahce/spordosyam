<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// NetGSM SMS hatırlatma komutu (aidat + ders)
Schedule::command('sms:send-reminders')->dailyAt('09:00');
