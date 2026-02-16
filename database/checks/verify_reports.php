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
    echo "Verifying Analytics Reports data aggregation...\n";
    
    $controller = app(\App\Http\Controllers\Admin\ReportController::class);
    $view = $controller->index();
    $data = $view->getData();
    
    // Check 1: Monthly Trend Data (should have 6 items)
    $monthlyData = $data['monthlyData'];
    echo "Monthly Trend Items: " . count($monthlyData) . " (Expected: 6)\n";
    
    // Check 2: Sales Source Data
    $salesSource = $data['salesSource'];
    echo "Current Month Walk-in Revenue: " . $salesSource['walkin'] . "\n";
    echo "Current Month Ship Revenue: " . $salesSource['ship'] . "\n";

    // Compare with direct DB query for current month
    $monthStart = Carbon::now()->startOfMonth();
    $dbWalkin = TblSalesWalkin::where('sold_at', '>=', $monthStart)->where('payment_status', 'paid')->sum('total_amount');
    $dbShip = TblShipDelivery::where('delivered_at', '>=', $monthStart)->where('payment_status', 'paid')->sum('total_amount');

    if ($salesSource['walkin'] == $dbWalkin && $salesSource['ship'] == $dbShip) {
        echo "✅ SUCCESS: Sales source breakdown matches database.\n";
    } else {
        echo "❌ FAILURE: Sales source mismatch (Expected: $dbWalkin/$dbShip, Actual: {$salesSource['walkin']}/{$salesSource['ship']})\n";
    }

    // Check 3: Top Ships
    $topShips = $data['topShips'];
    echo "Top Ships Count: " . count($topShips) . "\n";
    if (count($topShips) > 0) {
        echo "Leading Ship: " . $topShips[0]->ship_name . " (₱ " . $topShips[0]->total_revenue . ")\n";
    }

    echo "\nVerification Complete!\n";

} catch (\Throwable $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
