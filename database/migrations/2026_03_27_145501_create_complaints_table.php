<?php
// database/migrations/2024_01_01_000002_create_complaints_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintsTable extends Migration
{
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('userrs')->onDelete('cascade');
            $table->foreignId('provider_id')->nullable()->constrained('userrs')->onDelete('set null');
            $table->string('complaint_type');
            $table->string('subject', 255);
            $table->text('description');
            $table->enum('status', ['pending', 'in_review', 'resolved', 'rejected', 'cancelled'])->default('pending');
            $table->text('admin_response')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['customer_id', 'status']);
            $table->index('booking_id');
            $table->index('provider_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaints');
    }
}