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
        Schema::create('tbl_backwash_status', function (Blueprint $table) {
            $table->id();

            $table->timestamp('last_backwash_at')->nullable();
            $table->decimal('gallons_since_last', 12, 2)->default(0);  // keep running total
            $table->unsignedInteger('threshold_gallons')->default(200);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_backwash_status');
    }
};
