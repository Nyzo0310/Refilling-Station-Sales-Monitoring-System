<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rename "Delivery Boy" expense type to "Delivery Expenses".
     */
    public function up(): void
    {
        DB::table('tbl_expenses')
            ->where('expense_type', 'Delivery Boy')
            ->update([
                'expense_type' => 'Delivery Expenses'
            ]);
    }

    public function down(): void
    {
        DB::table('tbl_expenses')
            ->where('expense_type', 'Delivery Expenses')
            ->update([
                'expense_type' => 'Delivery Boy'
            ]);
    }
};
