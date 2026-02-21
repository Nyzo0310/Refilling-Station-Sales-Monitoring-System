<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill "Delivery Boy" expenses for all existing transactions.
     * - ₱5 per walk-in sale
     * - ₱10 per port/ship delivery
     */
    public function up(): void
    {
        $now = now();

        // 1. Walk-in sales → ₱5 each
        $walkins = DB::table('tbl_sales_walkin')->select('id', 'sold_at')->get();
        $walkinExpenses = [];
        foreach ($walkins as $sale) {
            $walkinExpenses[] = [
                'date'         => date('Y-m-d', strtotime($sale->sold_at)),
                'expense_type' => 'Delivery Boy',
                'amount'       => 5.00,
                'remarks'      => 'Walk-in sale #' . $sale->id,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }
        if (!empty($walkinExpenses)) {
            DB::table('tbl_expenses')->insert($walkinExpenses);
        }

        // 2. Ship/port deliveries → ₱10 each
        $deliveries = DB::table('tbl_ship_deliveries')->select('id', 'delivered_at', 'ship_name')->get();
        $deliveryExpenses = [];
        foreach ($deliveries as $d) {
            $deliveryExpenses[] = [
                'date'         => date('Y-m-d', strtotime($d->delivered_at)),
                'expense_type' => 'Delivery Boy',
                'amount'       => 10.00,
                'remarks'      => 'Port delivery #' . $d->id . ' (' . $d->ship_name . ')',
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }
        if (!empty($deliveryExpenses)) {
            DB::table('tbl_expenses')->insert($deliveryExpenses);
        }
    }

    public function down(): void
    {
        // Remove all backfilled delivery boy expenses
        DB::table('tbl_expenses')
            ->where('expense_type', 'Delivery Boy')
            ->where(function ($q) {
                $q->where('remarks', 'LIKE', 'Walk-in sale #%')
                  ->orWhere('remarks', 'LIKE', 'Port delivery #%');
            })
            ->delete();
    }
};
