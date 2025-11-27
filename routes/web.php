<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommissionReportController;
use App\Http\Controllers\TopDistributorController;

use Inertia\Inertia;
// use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');
Route::get('/reports/commission', [CommissionReportController::class, 'index'])
    ->name('commission-report');

Route::post('/reports/commission', [CommissionReportController::class, 'show'])->name('order-details');

Route::get('/reports/top-distributors', [TopDistributorController::class, 'index'])->name('top-distributors');
