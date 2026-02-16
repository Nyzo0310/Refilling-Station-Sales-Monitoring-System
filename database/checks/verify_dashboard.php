<?php

use App\Models\TblSalesWalkin;
use App\Models\TblShipDelivery;
use Carbon\Carbon;

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Verifying combined Dashboard sales...\n";
    
    $today = Carbon::today();
    
    // Get totals from DB directly
    $walkinRev = TblSalesWalkin::whereDate('sold_at', $today)->where('payment_status', 'paid')->sum('total_amount');
    $shipRev = TblShipDelivery::whereDate('delivered_at', $today)->where('payment_status', 'paid')->sum('total_amount');
    $expectedTodayRev = $walkinRev + $shipRev;
    
    $walkinGal = TblSalesWalkin::whereDate('sold_at', $today)->where('payment_status', 'paid')->sum('quantity');
    $shipGal = TblShipDelivery::whereDate('delivered_at', $today)->where('payment_status', 'paid')->sum('quantity');
    $expectedTodayGal = $walkinGal + $shipGal;

    // Call Controller
    $controller = app(\App\Http\Controllers\Admin\DashboardController::class);
    $view = $controller->index();
    $data = $view->getData();
    
    $actualTodayRev = $data['todayRevenue'];
    $actualTodayGal = $data['todayGallons'];

    echo "Walk-in Today Rev: $walkinRev | Ship Today Rev: $shipRev\n";
    echo "Expected Today Rev: $expectedTodayRev | Actual: $actualTodayRev\n";
    echo "Expected Today Gal: $expectedTodayGal | Actual: $actualTodayGal\n";

    if ($actualTodayRev == $expectedTodayRev && $actualTodayGal == $expectedTodayGal) {
        echo "✅ SUCCESS: Dashboard is correctly summing both sales types.\n";
    } else {
        echo "❌ FAILURE: Mismatch in calculations.\n";
    }

} catch (\Throwable $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
