<?php

use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecordController;
use Illuminate\Support\Facades\Artisan;

Route::resource('records', RecordController::class);
Route::post('records/generate', [RecordController::class, 'generate'])->name('records.generate');
Route::post('records/sync', [RecordController::class, 'sync'])->name('records.sync');
Route::post('records/clear', [RecordController::class, 'clear'])->name('records.clear');

Route::put('records/{id}', [RecordController::class, 'update'])->name('records.update');
Route::delete('records/{id}', [RecordController::class, 'destroy'])->name('records.destroy');

Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');

Route::get('/fetch/{count?}', function ($count = null) {
    Artisan::call("fetch:google-comments {$count}");
    return nl2br(Artisan::output());
});

Route::get('/', function () {
    return redirect('/records');
});

