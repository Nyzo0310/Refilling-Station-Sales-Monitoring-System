<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix delivery boy expenses: change from per-transaction to per-gallon.
     * Walk-in: quantity × ₱5
     * Ship:    quantity × ₱10
     */
    public function up(): void
    {
        $now = now();

        // 1. Delete all old flat-rate delivery boy expenses
        DB::table('tbl_expenses')
            ->where('expense_type', 'Delivery Boy')
            ->delete();

        // 2. Re-insert walk-in delivery boy expenses (₱5 per gallon)
        $walkins = DB::table('tbl_sales_walkin')->select('id', 'sold_at', 'quantity')->get();
        $batch = [];
        foreach ($walkins as $sale) {
            $batch[] = [
                'date'         => date('Y-m-d', strtotime($sale->sold_at)),
                'expense_type' => 'Delivery Boy',
                'amount'       => $sale->quantity * 5,
                'remarks'      => 'Walk-in sale #' . $sale->id . ' (' . $sale->quantity . ' gal)',
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }
        if (!empty($batch)) {
            DB::table('tbl_expenses')->insert($batch);
        }

        // 3. Re-insert ship delivery boy expenses (₱10 per gallon)
        $ships = DB::table('tbl_ship_deliveries')->select('id', 'delivered_at', 'quantity', 'ship_name')->get();
        $batch = [];
        foreach ($ships as $d) {
            $batch[] = [
                'date'         => date('Y-m-d', strtotime($d->delivered_at)),
                'expense_type' => 'Delivery Boy',
                'amount'       => $d->quantity * 10,
                'remarks'      => 'Port delivery #' . $d->id . ' (' . $d->ship_name . ', ' . $d->quantity . ' gal)',
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }
        if (!empty($batch)) {
            DB::table('tbl_expenses')->insert($batch);
        }
    }

    public function down(): void
    {
        // Remove all delivery boy expenses
        DB::table('tbl_expenses')
            ->where('expense_type', 'Delivery Boy')
            ->delete();
    }
};
