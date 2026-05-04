<?php
// database/migrations/2026_04_05_000001_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->string('payment_method'); // jazzcash, easypaisa, cod, card
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, processing, completed, failed, refunded
            $table->string('transaction_id')->nullable()->unique();
            $table->string('sandbox_mode')->default(true);
            
            // Sandbox specific fields
            $table->string('card_number_masked')->nullable();
            $table->string('sandbox_token')->nullable();
            $table->json('payment_response')->nullable();
            
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('userrs')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('userrs')->onDelete('set null');
            
            $table->index('transaction_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};