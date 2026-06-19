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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number');
            
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('courier_id')->nullable()->constrained('users')->onDelete('cascade');

            // tarif & rute
            $table->foreignId('rate_id')->constrained('rates');

            // detail pengiriman
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('receiver_address');

            // Spesifikasi Paket & Logika Berat
            $table->decimal('actual_weight', 8, 2); // Berat timbangan asli (kg)
            $table->decimal('length', 8, 2)->default(0); // cm (untuk volume)
            $table->decimal('width', 8, 2)->default(0);  // cm (untuk volume)
            $table->decimal('height', 8, 2)->default(0); // cm (untuk volume)
            $table->decimal('chargeable_weight', 8, 2); // Berat akhir yang ditagih (hasil rumus berat vs volume)
            
            // Total Biaya
            $table->decimal('total_shipping_fee', 12, 2);
            
            // Status Pengiriman (e.g., pending, picked_up, in_transit, delivered)
            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
