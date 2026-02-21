<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking for orphaned Delivery Boy expenses...\n";

// Get all Delivery Boy expenses
$expenses = DB::table('tbl_expenses')->where('expense_type', 'Delivery Boy')->get();

$deletedWalkins = 0;
$deletedPort = 0;

foreach ($expenses as $e) {
    if (preg_match('/Walk-in sale #(\d+)/', $e->remarks, $matches)) {
        $saleId = $matches[1];
        if (!DB::table('tbl_sales_walkin')->where('id', $saleId)->exists()) {
            DB::table('tbl_expenses')->where('id', $e->id)->delete();
            $deletedWalkins++;
            echo "Deleted orphaned walk-in expense for sale #$saleId (₱{$e->amount})\n";
        }
    } elseif (preg_match('/Port delivery #(\d+)/', $e->remarks, $matches)) {
        $deliveryId = $matches[1];
        if (!DB::table('tbl_ship_deliveries')->where('id', $deliveryId)->exists()) {
            DB::table('tbl_expenses')->where('id', $e->id)->delete();
            $deletedPort++;
            echo "Deleted orphaned port delivery expense for delivery #$deliveryId (₱{$e->amount})\n";
        }
    }
}

echo "Cleanup complete! Removed $deletedWalkins walk-in orphans and $deletedPort port orphans.\n";
