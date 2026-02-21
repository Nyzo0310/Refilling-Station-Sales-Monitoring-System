<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Revert/Update all "Onie Refill" records to ₱5/gallon.
     */
    public function up(): void
    {
        DB::table('tbl_sales_walkin')
            ->where('container_type', 'LIKE', '%onie%')
            ->orWhere('container_type', 'LIKE', '%oni%')
            ->update([
                'price_per_container' => 5.00,
                'total_amount'        => DB::raw('quantity * 5.00'),
            ]);
    }

    public function down(): void
    {
        // No explicit rollback needed
    }
};
