<?php
// database/migrations/xxxx_add_accept_reject_fields_to_bookings.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add request status
            if (!Schema::hasColumn('bookings', 'request_status')) {
                $table->enum('request_status', ['pending', 'accepted', 'rejected'])->default('pending')->after('status');
            }
            
            // Add delivery status
            if (!Schema::hasColumn('bookings', 'delivery_status')) {
                $table->enum('delivery_status', [
                    'order_confirmed', 
                    'vehicle_dispatched', 
                    'in_transit', 
                    'delivered'
                ])->nullable()->after('request_status');
            }
            
            // Add booking complete flag
            if (!Schema::hasColumn('bookings', 'is_booking_complete')) {
                $table->enum('is_booking_complete', ['yes', 'no'])->default('no')->after('delivery_status');
            }
            
            // Add rejection reason
            if (!Schema::hasColumn('bookings', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('is_booking_complete');
            }
            
            // Add timestamps for status changes
            if (!Schema::hasColumn('bookings', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('rejection_reason');
            }
            
            if (!Schema::hasColumn('bookings', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('accepted_at');
            }
            
            if (!Schema::hasColumn('bookings', 'dispatched_at')) {
                $table->timestamp('dispatched_at')->nullable()->after('rejected_at');
            }
            
            if (!Schema::hasColumn('bookings', 'in_transit_at')) {
                $table->timestamp('in_transit_at')->nullable()->after('dispatched_at');
            }
            
            if (!Schema::hasColumn('bookings', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('in_transit_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'request_status',
                'delivery_status',
                'is_booking_complete',
                'rejection_reason',
                'accepted_at',
                'rejected_at',
                'dispatched_at',
                'in_transit_at',
                'delivered_at'
            ]);
        });
    }
};