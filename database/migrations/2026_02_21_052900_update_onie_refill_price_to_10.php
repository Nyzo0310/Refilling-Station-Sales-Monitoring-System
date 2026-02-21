<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update all "Onie Refill" records to ₱10/gallon.
     */
    public function up(): void
    {
        DB::table('tbl_sales_walkin')
            ->where('container_type', 'LIKE', '%onie%')
            ->orWhere('container_type', 'LIKE', '%Onie%')
            ->update([
                'price_per_container' => 10.00,
                'total_amount'        => DB::raw('quantity * 10.00'),
            ]);
    }

    /**
     * No rollback — price was manually set before.
     */
    public function down(): void
    {
        // Cannot reliably revert to old prices
    }
};
