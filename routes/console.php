<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


app()->make(Schedule::class)->call(function () {
    Artisan::call('sync:google-sheet');
    Artisan::call('fetch:google-comments');
})->everyMinute();
