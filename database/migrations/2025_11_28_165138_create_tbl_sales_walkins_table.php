<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_sales_walkin', function (Blueprint $table) {
            $table->id();
            $table->dateTime('sold_at');                 // auto-filled with "now" on save
            $table->string('container_type')->nullable(); // e.g. 5 gal blue, 1 gal, etc.
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price_per_container', 8, 2);
            $table->decimal('total_amount', 10, 2);

            // CATEGORIES ONLY: neighbor / non-neighbor / ship crew
            $table->enum('customer_type', ['neighbor', 'non_neighbor', 'crew_ship'])->default('neighbor');

            $table->enum('payment_status', ['paid', 'unpaid'])->default('paid');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_sales_walkin');
    }
};
