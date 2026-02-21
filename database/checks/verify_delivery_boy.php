<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Count ALL walk-in sales
$walkinCount = DB::table('tbl_sales_walkin')->count();
echo "Walk-in sales count: $walkinCount (should be × ₱5 = ₱" . ($walkinCount * 5) . ")\n";

// Count ALL ship deliveries
$shipCount = DB::table('tbl_ship_deliveries')->count();
echo "Ship deliveries count: $shipCount (should be × ₱10 = ₱" . ($shipCount * 10) . ")\n";

$expectedTotal = ($walkinCount * 5) + ($shipCount * 10);
echo "\nExpected delivery boy total: ₱$expectedTotal\n";

// Count actual delivery boy expenses
$actualDeliveryBoy = DB::table('tbl_expenses')
    ->where('expense_type', 'Delivery Boy')
    ->sum('amount');
echo "Actual delivery boy expenses in DB: ₱$actualDeliveryBoy\n";

$deliveryBoyCount = DB::table('tbl_expenses')
    ->where('expense_type', 'Delivery Boy')
    ->count();
echo "Delivery boy expense records: $deliveryBoyCount\n";

// Show ALL expenses
echo "\n=== ALL EXPENSES ===\n";
$all = DB::table('tbl_expenses')->get();
foreach ($all as $e) {
    echo "ID:{$e->id} | {$e->date} | {$e->expense_type} | ₱{$e->amount} | {$e->remarks}\n";
}
echo "\nTotal expenses sum: ₱" . DB::table('tbl_expenses')->sum('amount') . "\n";
