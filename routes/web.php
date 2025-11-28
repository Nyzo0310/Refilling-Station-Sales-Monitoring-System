<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\WalkinSalesController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    // Walk-in sales (only index + store)
    Route::get('/walkin-sales', [WalkinSalesController::class, 'index'])
        ->name('walkin.index');

    Route::post('/walkin-sales', [WalkinSalesController::class, 'store'])
        ->name('walkin.store');
});
