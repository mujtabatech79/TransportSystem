<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Increase column sizes for long distances
            $table->decimal('estimated_distance', 12, 2)->nullable()->change(); // Can handle up to 99,999,999.99
            $table->decimal('estimated_fare', 12, 2)->nullable()->change(); // Can handle up to 99,999,999.99
            $table->decimal('toll_cost', 12, 2)->nullable()->change();
            
            // Change route_options from json to longtext if needed
            $table->longText('route_options')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('estimated_distance', 10, 2)->nullable()->change();
            $table->decimal('estimated_fare', 10, 2)->nullable()->change();
            $table->decimal('toll_cost', 10, 2)->nullable()->change();
            $table->json('route_options')->nullable()->change();
        });
    }
};