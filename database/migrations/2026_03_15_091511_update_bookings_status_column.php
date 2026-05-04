<?php
// database/migrations/2024_xx_xx_update_bookings_status_column.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, check current column type
        Schema::table('bookings', function (Blueprint $table) {
            // Change status column to accept new values
            $table->string('status', 20)->default('request')->change();
        });

        // Update existing records - 'booked' ko 'request' mein convert karo
        DB::table('bookings')->where('status', 'booked')->update(['status' => 'request']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Wapas old values par convert karo
        DB::table('bookings')
            ->whereIn('status', ['request', 'accept', 'reject', 'complete'])
            ->update(['status' => 'booked']);
            
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('status', 20)->default('booked')->change();
        });
    }
};