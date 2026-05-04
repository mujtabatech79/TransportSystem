<?php
// database/migrations/xxxx_xx_xx_add_all_fields_to_bookings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Time field - required
            $table->string('pickup_time')->nullable(false)->after('booking_date');
            
            // Goods fields - required
            $table->string('goods_type')->nullable(false)->after('dropoff_location');
            $table->integer('goods_weight')->nullable(false)->after('goods_type');
            $table->text('special_instructions')->nullable(false)->after('goods_weight');
            
            // Coordinates - required
            $table->decimal('pickup_lat', 10, 8)->nullable(false)->after('pickup_location');
            $table->decimal('pickup_lng', 11, 8)->nullable(false)->after('pickup_lat');
            $table->decimal('dropoff_lat', 10, 8)->nullable(false)->after('dropoff_location');
            $table->decimal('dropoff_lng', 11, 8)->nullable(false)->after('dropoff_lat');
            
            // Distance and fare - required
            $table->decimal('distance_km', 8, 2)->nullable(false)->after('dropoff_lng');
            $table->decimal('estimated_fare', 10, 2)->nullable(false)->after('distance_km');
            $table->json('route_geometry')->nullable(false)->after('estimated_fare');
            
            // Payment method - required
            $table->string('payment_method')->nullable(false)->after('route_geometry');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_time',
                'goods_type',
                'goods_weight',
                'special_instructions',
                'pickup_lat',
                'pickup_lng',
                'dropoff_lat',
                'dropoff_lng',
                'distance_km',
                'estimated_fare',
                'route_geometry',
                'payment_method'
            ]);
        });
    }
};