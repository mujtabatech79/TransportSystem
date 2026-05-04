<?php
// database/migrations/2024_01_01_create_messages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('type')->default('text'); // text, image, etc.
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('userrs')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('userrs')->onDelete('cascade');
            
            // Indexes for performance
            $table->index(['sender_id', 'receiver_id']);
            $table->index(['booking_id']);
            $table->index(['is_read']);
            $table->index(['created_at']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}