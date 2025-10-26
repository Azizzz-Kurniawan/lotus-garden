<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // 1. Tambahkan "use" ini di atas

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/*
|--------------------------------------------------------------------------
| Penjadwalan Tugas (Task Scheduling)
|--------------------------------------------------------------------------
|
| Tambahkan logika penjadwalan command kamu di sini.
|
*/

// 2. Tambahkan blok kodemu di sini
$hour = config('questionCategory.active_hour', 10);
$minute = config('questionCategory.active_minute', 0);

Schedule::command('categories:activate-pending')
    ->dailyAt("$hour:$minute");
