<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update Walk-in Sales Table
        Schema::table('tbl_sales_walkin', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_sales_walkin', 'money_received')) {
                $table->decimal('money_received', 12, 2)->default(0)->after('payment_status');
            }
            // Enum update via change() requires doctrine/dbal, using a safer approach
            $table->string('payment_status')->default('paid')->change();
        });

        // Update Ship Deliveries Table
        Schema::table('tbl_ship_deliveries', function (Blueprint $table) {
            $table->string('payment_status')->default('paid')->change();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_sales_walkin', function (Blueprint $table) {
            $table->dropColumn('money_received');
        });
    }
};
