<?php
// database/migrations/2024_01_01_000000_add_penalty_fields_to_bookings.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPenaltyFieldsToBookings extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add penalty_amount column if not exists
            if (!Schema::hasColumn('bookings', 'penalty_amount')) {
                $table->decimal('penalty_amount', 10, 2)->default(0)->after('actual_fare');
            }
            
            // Ensure actual_duration column exists
            if (!Schema::hasColumn('bookings', 'actual_duration')) {
                $table->integer('actual_duration')->nullable()->after('estimated_duration');
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['penalty_amount']);
        });
    }
}