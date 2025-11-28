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
        Schema::create('tbl_ship_deliveries', function (Blueprint $table) {
            $table->id();

            $table->string('ship_name', 100);
            $table->string('crew_name', 100)->nullable();
            $table->string('contact_number', 30)->nullable();

            $table->decimal('container_size_liters', 8, 2)->nullable();
            $table->string('container_type', 50)->nullable();
            $table->unsignedInteger('quantity')->default(1);

            $table->decimal('price_per_container', 10, 2);   // default 35 but editable
            $table->decimal('total_amount', 12, 2);          // quantity * price

            $table->timestamp('delivered_at');               // date + time delivered

            $table->enum('payment_status', ['paid', 'unpaid'])
                ->default('paid');
            $table->decimal('money_received', 12, 2)->default(0);

            $table->boolean('delivery_fee_included')->default(true);
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_ship_deliveries');
    }
};
