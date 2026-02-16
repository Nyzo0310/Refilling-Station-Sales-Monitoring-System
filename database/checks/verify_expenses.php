<?php

use App\Models\TblSalesWalkin;
use App\Models\TblShipDelivery;
use App\Models\TblExpense;
use Carbon\Carbon;

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Verifying Expenses and Profit calculations...\n";
    
    $monthStart = Carbon::now()->startOfMonth();
    
    // 1. Clear existing expenses for today for testing (optional, but cleaner)
    // TblExpense::whereDate('date', Carbon::today())->delete();

    // 2. Add a test expense
    $testExpenseAmount = 5000.00;
    TblExpense::create([
        'expense_type' => 'machine maintenance',
        'amount'       => $testExpenseAmount,
        'date'         => Carbon::today(),
        'remarks'      => 'Test Maintenance'
    ]);

    // 3. Get expected values
    $walkinRev = TblSalesWalkin::where('sold_at', '>=', $monthStart)->where('payment_status', 'paid')->sum('total_amount');
    $shipRev = TblShipDelivery::where('delivered_at', '>=', $monthStart)->where('payment_status', 'paid')->sum('total_amount');
    $expectedTotalRev = $walkinRev + $shipRev;
    
    $expectedExpenses = TblExpense::where('date', '>=', $monthStart)->sum('amount');
    $expectedProfit = $expectedTotalRev - $expectedExpenses;

    // 4. Call Controller
    $controller = app(\App\Http\Controllers\Admin\DashboardController::class);
    $view = $controller->index();
    $data = $view->getData();
    
    $actualRev = $data['monthRevenue'];
    $actualExp = $data['monthExpenses'];
    $actualProfit = $data['monthProfit'];

    echo "Total Monthly Revenue: $actualRev\n";
    echo "Total Monthly Expenses: $actualExp\n";
    echo "Calculated Monthly Profit: $actualProfit\n";

    if ($actualProfit == $expectedProfit) {
        echo "✅ SUCCESS: Profit is correctly calculated (Revenue - Expenses).\n";
    } else {
        echo "❌ FAILURE: Mismatch in profit calculation (Expected: $expectedProfit, Actual: $actualProfit).\n";
    }

} catch (\Throwable $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
