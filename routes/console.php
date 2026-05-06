<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

foreach ((array) config('backup_system.monthly_days', [1, 15]) as $day) {
    Schedule::command('backups:queue --type=monthly --scope=monthly')
        ->monthlyOn((int) $day, (string) config('backup_system.monthly_time', '02:00'))
        ->withoutOverlapping()
        ->runInBackground();
}

Schedule::command('backups:queue --type=yearly --scope=yearly')
    ->yearlyOn(
        (int) config('backup_system.yearly_month', 1),
        (int) config('backup_system.yearly_day', 1),
        (string) config('backup_system.yearly_time', '03:00')
    )
    ->withoutOverlapping()
    ->runInBackground();
