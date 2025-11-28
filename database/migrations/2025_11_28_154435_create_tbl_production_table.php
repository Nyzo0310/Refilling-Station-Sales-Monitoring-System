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
        Schema::create('tbl_production', function (Blueprint $table) {
            $table->id();

            $table->date('date')->unique();                       // one record per day
            $table->decimal('gallons_produced', 12, 2)->default(0);
            $table->decimal('production_cost_per_gallon', 10, 2)->default(0);
            $table->decimal('total_production_cost', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_production');
    }
};
