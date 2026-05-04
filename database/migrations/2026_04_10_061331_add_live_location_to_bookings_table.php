// database/migrations/xxxx_add_live_location_to_bookings_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLiveLocationToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('live_lat', 10, 7)->nullable()->after('dropoff_lng');
            $table->decimal('live_lng', 10, 7)->nullable()->after('live_lat');
            $table->boolean('is_sharing_location')->default(false)->after('live_lng');
            $table->timestamp('location_updated_at')->nullable()->after('is_sharing_location');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['live_lat', 'live_lng', 'is_sharing_location', 'location_updated_at']);
        });
    }
}