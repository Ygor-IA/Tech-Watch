<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
use Illuminate\Support\Facades\Schedule;

// Roda o robô de raspagem de preços todos os dias às 10 da manhã
Schedule::command('precos:verificar')->dailyAt('10:00');