<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('fraud_status')->nullable()->comment('fraud / not_fraud / null');
            $table->unsignedTinyInteger('fraud_score')->nullable()->comment('0-100 fraud score');
            $table->text('fraud_reasons')->nullable()->comment('JSON array of fraud check reasons');
            $table->text('smartcard_extracted')->nullable()->comment('JSON of AI-extracted smartcard data');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['fraud_status', 'fraud_score', 'fraud_reasons', 'smartcard_extracted']);
        });
    }
};