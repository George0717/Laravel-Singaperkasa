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
        Schema::create('surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->onDelete('cascade');
            $table->string('plat_angkutan');
            $table->date('tanggal_pengiriman');
            $table->string('no_surat_jalan')->unique(); // Unique serial number
            $table->timestamps();
        });

        Schema::create('surat_jalan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_jalan_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_order_detail_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_details');
        Schema::dropIfExists('surat_jalans');
    }
};

