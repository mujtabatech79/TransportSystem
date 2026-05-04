<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('userrs', function (Blueprint $table) {

            // Add column only if it does not already exist
            if (!Schema::hasColumn('userrs', 'verification_token')) {
                $table->string('verification_token')->nullable();
            }

            // Optional: email verification flag (safe addition)
            if (!Schema::hasColumn('userrs', 'email_verified')) {
                $table->boolean('email_verified')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('userrs', function (Blueprint $table) {

            if (Schema::hasColumn('userrs', 'verification_token')) {
                $table->dropColumn('verification_token');
            }

            if (Schema::hasColumn('userrs', 'email_verified')) {
                $table->dropColumn('email_verified');
            }
        });
    }
};