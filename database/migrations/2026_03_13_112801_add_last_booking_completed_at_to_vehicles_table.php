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
        Schema::table('vehicles', function (Blueprint $table) {
            // Add last_booking_completed_at to track when vehicle became available
            if (!Schema::hasColumn('vehicles', 'last_booking_completed_at')) {
                $table->timestamp('last_booking_completed_at')->nullable()->after('is_booked');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'last_booking_completed_at')) {
                $table->dropColumn('last_booking_completed_at');
            }
        });
    }
};