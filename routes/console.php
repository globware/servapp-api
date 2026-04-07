<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\CancelStaleServiceRequests;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

Schedule::command(CancelStaleServiceRequests::class)
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/cancel-pending.log'));


// * * * * * cd /var/www/your-app && php artisan schedule:run >> /dev/null 2>&1