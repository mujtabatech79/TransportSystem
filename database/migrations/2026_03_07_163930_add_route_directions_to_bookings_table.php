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
        Schema::table('bookings', function (Blueprint $table) {
            // Add route_directions column if it doesn't exist
            if (!Schema::hasColumn('bookings', 'route_directions')) {
                $table->text('route_directions')->nullable()->after('route_polyline');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'route_directions')) {
                $table->dropColumn('route_directions');
            }
        });
    }
};