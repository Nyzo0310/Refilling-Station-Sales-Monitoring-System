<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        Schema::create('tbl_inventory_containers', function (Blueprint $table) {
            $table->id();

            $table->string('container_type', 50);                // e.g. 20L round
            $table->decimal('size_liters', 8, 2);                // 20.00

            $table->integer('stock_in')->default(0);             // total received
            $table->integer('stock_out')->default(0);            // total sold / used
            $table->integer('current_stock')->default(0);        // for quick display

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_inventory_containers');
    }

};
