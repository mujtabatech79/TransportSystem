<?php
// database/migrations/2024_01_01_000001_create_reviews_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('userrs')->onDelete('cascade');
            $table->foreignId('provider_id')->nullable()->constrained('userrs')->onDelete('set null');
            $table->integer('rating')->unsigned()->comment('1-5 stars');
            $table->text('review')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
            
            $table->index(['booking_id', 'customer_id']);
            $table->index('provider_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}