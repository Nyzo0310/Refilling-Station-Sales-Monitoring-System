<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\WalkinSalesController;
use App\Http\Controllers\Admin\ShipDeliveryController;
use App\Http\Controllers\Admin\BackwashController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Auth\LoginController;

// 🛡️ Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// 🔒 Protected Admin Routes
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');


    
    // 🔽 NEW: log backwash
    Route::post('/backwash-log', [BackwashController::class, 'store'])
        ->name('backwash.store');

    // Walk-in sales
    Route::get('/walkin-sales', [WalkinSalesController::class, 'index'])
        ->name('walkin.index');
    Route::post('/walkin-sales', [WalkinSalesController::class, 'store'])
        ->name('walkin.store');
    Route::get('/walkin-sales/{sale}', [WalkinSalesController::class, 'show'])
        ->name('walkin.show');
    Route::patch('/walkin-sales/{sale}', [WalkinSalesController::class, 'update'])
        ->name('walkin.update');
    Route::delete('/walkin-sales/{sale}', [WalkinSalesController::class, 'destroy'])
        ->name('walkin.destroy');

    // 🚢 Ship Deliveries
    Route::get('/ship-deliveries', [ShipDeliveryController::class, 'index'])
        ->name('ship-deliveries.index');
    Route::post('/ship-deliveries', [ShipDeliveryController::class, 'store'])
        ->name('ship-deliveries.store');
    Route::get('/ship-deliveries/{delivery}', [ShipDeliveryController::class, 'show'])
        ->name('ship-deliveries.show');
    Route::patch('/ship-deliveries/{delivery}', [ShipDeliveryController::class, 'update'])
        ->name('ship-deliveries.update');
    Route::delete('/ship-deliveries/{delivery}', [ShipDeliveryController::class, 'destroy'])
        ->name('ship-deliveries.destroy');

    // 💰 Expenses
    Route::get('/expenses', [ExpenseController::class, 'index'])
        ->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])
        ->name('expenses.store');
    Route::patch('/expenses/{expense}', [ExpenseController::class, 'update'])
        ->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])
        ->name('expenses.destroy');

    // 📊 Reports
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');
});
