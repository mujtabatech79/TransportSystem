<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            if (!Schema::hasColumn('bookings', 'pickup_lat')) {
                $table->decimal('pickup_lat', 10, 8)->nullable();
            }

            if (!Schema::hasColumn('bookings', 'pickup_lng')) {
                $table->decimal('pickup_lng', 11, 8)->nullable();
            }

            if (!Schema::hasColumn('bookings', 'dropoff_lat')) {
                $table->decimal('dropoff_lat', 10, 8)->nullable();
            }

            if (!Schema::hasColumn('bookings', 'dropoff_lng')) {
                $table->decimal('dropoff_lng', 11, 8)->nullable();
            }

            if (!Schema::hasColumn('bookings', 'pickup_time')) {
                $table->time('pickup_time')->nullable();
            }

            if (!Schema::hasColumn('bookings', 'goods_type')) {
                $table->string('goods_type')->nullable();
            }

            if (!Schema::hasColumn('bookings', 'goods_weight')) {
                $table->decimal('goods_weight', 10, 2)->nullable();
            }

            if (!Schema::hasColumn('bookings', 'special_instructions')) {
                $table->text('special_instructions')->nullable();
            }

            if (!Schema::hasColumn('bookings', 'estimated_distance')) {
                $table->decimal('estimated_distance', 10, 2)->nullable();
            }

            if (!Schema::hasColumn('bookings', 'estimated_fare')) {
                $table->decimal('estimated_fare', 10, 2)->nullable();
            }

            if (!Schema::hasColumn('bookings', 'actual_distance')) {
                $table->decimal('actual_distance', 10, 2)->nullable();
            }

            if (!Schema::hasColumn('bookings', 'actual_fare')) {
                $table->decimal('actual_fare', 10, 2)->nullable();
            }

            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }

            if (!Schema::hasColumn('bookings', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            }

            if (!Schema::hasColumn('bookings', 'route_polyline')) {
                $table->text('route_polyline')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            $columns = [
                'pickup_lat', 'pickup_lng', 'dropoff_lat', 'dropoff_lng',
                'pickup_time', 'goods_type', 'goods_weight', 'special_instructions',
                'estimated_distance', 'estimated_fare', 'actual_distance', 'actual_fare',
                'payment_method', 'payment_status', 'route_polyline'
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('bookings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};