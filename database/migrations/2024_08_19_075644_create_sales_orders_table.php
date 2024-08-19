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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_address');
            $table->date('po_date');
            $table->string('po_number');
            $table->string('so_number')->nullable();
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('down_payment', 15, 2)->default(0);
            $table->decimal('vat', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->string('payment_schedule_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
