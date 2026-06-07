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
        Schema::create('password_resets', function (Blueprint $table) {

            $table->id();

            // user requesting password reset
            $table->string('userid', 50);

            // hashed OTP
            $table->string('otp_hash');

            // OTP expiration time
            $table->timestamp('expires_at');

            // OTP verification status
            $table->boolean('is_verified')
                ->default(false);

            // request time
            $table->timestamp('created_at')
                ->useCurrent();

            // indexes
            $table->index('userid');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};