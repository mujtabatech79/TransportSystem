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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();


             $table->unsignedBigInteger('customer_id');
             $table->foreign('customer_id')->references('id')->on('userrs')->onDelete('cascade');
             
            $table->unsignedBigInteger('vehicle_id');
             $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            
            $table->date('booking_date');
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->enum('status', ['booked', 'completed', 'cancelled'])->default('booked');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
