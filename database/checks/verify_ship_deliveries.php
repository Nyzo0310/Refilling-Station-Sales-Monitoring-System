<?php

use App\Models\TblShipDelivery;
use App\Models\TblBackwashStatus;

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing Ship Delivery refinements...\n";
    
    $initialBackwash = TblBackwashStatus::first();
    $initialGallons = $initialBackwash ? (float)$initialBackwash->gallons_since_last : 0;
    
    // Simulate a request
    $request = new \Illuminate\Http\Request([
        'ship_name' => 'Test Ship',
        'quantity' => 10,
        'price_per_container' => 100,
        'payment_status' => 'paid'
    ]);
    
    $controller = app(\App\Http\Controllers\Admin\ShipDeliveryController::class);
    $response = $controller->store($request);
    
    $delivery = TblShipDelivery::latest()->first();
    echo "Delivery Total Amount: " . $delivery->total_amount . "\n";
    echo "Delivery Container Size (should be 3.785): " . $delivery->container_size_liters . "\n";
    
    $finalBackwash = TblBackwashStatus::first();
    $finalGallons = $finalBackwash ? (float)$finalBackwash->gallons_since_last : 0;
    
    echo "Initial Backwash Gallons: $initialGallons\n";
    echo "Final Backwash Gallons: $finalGallons\n";

    if ($finalGallons == ($initialGallons + 10)) {
        echo "✅ SUCCESS: Backwash incremented by quantity (10) as expected.\n";
    } else {
        echo "❌ FAILURE: Backwash increment was " . ($finalGallons - $initialGallons) . ", expected 10.\n";
    }
    
    if ($delivery->container_size_liters == 3.785) {
         echo "✅ SUCCESS: Container size defaulted to 3.785.\n";
    } else {
         echo "❌ FAILURE: Container size was " . $delivery->container_size_liters . ".\n";
    }

} catch (\Throwable $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
