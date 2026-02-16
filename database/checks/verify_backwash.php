<?php

use App\Support\BackwashUpdater;
use App\Models\TblBackwashStatus;

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing BackwashUpdater autoloading...\n";
    
    $initial = TblBackwashStatus::first();
    $initialGallons = $initial ? (float)$initial->gallons_since_last : 0;
    echo "Initial Gallons: $initialGallons\n";

    BackwashUpdater::addGallons(10.5);
    
    $final = TblBackwashStatus::first();
    $finalGallons = $final ? (float)$final->gallons_since_last : 0;
    echo "Final Gallons: $finalGallons\n";

    if ($finalGallons == ($initialGallons + 10.5)) {
        echo "✅ SUCCESS: BackwashUpdater is working correctly.\n";
    } else {
        echo "❌ FAILURE: Gallons did not increment as expected.\n";
    }

} catch (\Throwable $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
