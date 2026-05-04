<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add estimated_duration (in minutes)
            $table->integer('estimated_duration')->nullable()->after('estimated_fare');
            
            // Add actual_duration (in minutes) for completed trips
            $table->integer('actual_duration')->nullable()->after('actual_fare');
            
            // Add duration display text (optional)
            $table->string('duration_text')->nullable()->after('estimated_duration');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['estimated_duration', 'actual_duration', 'duration_text']);
        });
    }
};