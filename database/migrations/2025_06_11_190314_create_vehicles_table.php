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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
        // $table->foreignId('user_id')->constrained('userrs')->onDelete('cascade'); 
        $table->unsignedBigInteger('user_id');
        $table->foreign('user_id')->references('id')->on('userrs')->onDelete('cascade');

        $table->string('vehicle_number');
        $table->string('chassis_number');
        $table->enum('vehicle_type', ['truck', 'dumper']);
         $table->string('can_carry');
        $table->integer('weight_capacity'); // in KG or Ton
        

        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->enum('is_booked', ['yes', 'no'])->default('no');

        $table->timestamps();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
