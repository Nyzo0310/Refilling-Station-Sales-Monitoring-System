<?php
// Quick script: find Onie Refill records and update price to 10
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// 1. Show current Onie Refill records
$records = DB::select("SELECT id, container_type, price_per_container, quantity, total_amount FROM tbl_sales_walkin WHERE container_type LIKE '%onie%' OR container_type LIKE '%oni%' OR container_type LIKE '%Onie%'");
echo "=== ONIE REFILL RECORDS ===\n";
echo json_encode($records, JSON_PRETTY_PRINT) . "\n";
echo "Count: " . count($records) . "\n";

// 2. Update price_per_container to 10 and recalculate total_amount
$updated = DB::update("UPDATE tbl_sales_walkin SET price_per_container = 10.00, total_amount = quantity * 10.00 WHERE container_type LIKE '%onie%' OR container_type LIKE '%oni%' OR container_type LIKE '%Onie%'");
echo "\n=== UPDATED $updated RECORDS ===\n";

// 3. Verify
$after = DB::select("SELECT id, container_type, price_per_container, quantity, total_amount FROM tbl_sales_walkin WHERE container_type LIKE '%onie%' OR container_type LIKE '%oni%' OR container_type LIKE '%Onie%'");
echo json_encode($after, JSON_PRETTY_PRINT) . "\n";
