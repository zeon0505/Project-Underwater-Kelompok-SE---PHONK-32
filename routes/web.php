<?php

use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/dashboard-data', [DashboardController::class, 'apiData']);
Route::get('/export-csv', [DashboardController::class, 'exportCsv'])->name('export.csv');
Route::get('/report', [DashboardController::class, 'report'])->name('report');
Route::post('/api/sensor-data', [DashboardController::class, 'store'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/sensor/{type}', [DashboardController::class, 'sensorDetails'])->name('sensor.details');
