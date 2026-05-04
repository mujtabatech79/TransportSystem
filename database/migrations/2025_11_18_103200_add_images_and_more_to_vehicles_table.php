<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Add image columns
            $table->string('vehicle_image')->nullable()->after('weight_capacity');
            $table->string('smartcard_image')->nullable()->after('vehicle_image');

            // Optional: expand vehicle_type enum to be more flexible (if you want)
            // If you want to add new types now, use a string column and migrate values, but
            // for simplicity we keep enum and just comment guidance:
            // $table->enum('vehicle_type', ['truck','dumper','loader','van'])->change();
            //
            // If your DB doesn't support enum change easily, skip this and keep current enum.
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['vehicle_image', 'smartcard_image']);
        });
    }
};
