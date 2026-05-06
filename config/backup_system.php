<?php

$monthlyDays = array_filter(array_map('trim', explode(',', (string)env('BACKUP_MONTHLY_DAYS', '1,15'))));
$monthlyDays = array_map(static fn($day) => (int)$day, $monthlyDays);

return [
    'queue' => env('BACKUP_QUEUE', 'default'),
    'process_timeout' => (int)env('BACKUP_PROCESS_TIMEOUT', 3600),
    'disk' => env('BACKUP_DISK', 'backup'),
    'path_prefix' => env('BACKUP_PATH', ''),
    'mysql_dump_path' => env('BACKUP_MYSQLDUMP_PATH', ''),
    'monthly_days' => empty($monthlyDays) ? [1, 15] : $monthlyDays,
    'monthly_time' => env('BACKUP_MONTHLY_TIME', '02:00'),
    'yearly_month' => (int)env('BACKUP_YEARLY_MONTH', 1),
    'yearly_day' => (int)env('BACKUP_YEARLY_DAY', 1),
    'yearly_time' => env('BACKUP_YEARLY_TIME', '03:00'),
];
