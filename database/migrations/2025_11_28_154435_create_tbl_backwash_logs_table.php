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
        Schema::create('tbl_backwash_logs', function (Blueprint $table) {
            $table->id();

            $table->timestamp('backwash_at');        // exact date+time
            $table->text('remarks')->nullable();     // e.g. "Weekly backwash"

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_backwash_logs');
    }
};
