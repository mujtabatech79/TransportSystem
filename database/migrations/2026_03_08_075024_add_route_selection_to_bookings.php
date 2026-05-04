<?php
// database/migrations/YYYY_MM_DD_HHMMSS_add_route_selection_to_bookings.php

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
            // Add route selection fields
            $table->string('selected_route_name')->nullable()->after('route_polyline');
            $table->boolean('has_tolls')->default(false)->after('selected_route_name');
            $table->decimal('toll_cost', 10, 2)->nullable()->after('has_tolls');
            $table->json('route_options')->nullable()->after('toll_cost'); // Store all route options
            
            // Add index for better performance
            $table->index('selected_route_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'selected_route_name',
                'has_tolls',
                'toll_cost',
                'route_options'
            ]);
        });
    }
};