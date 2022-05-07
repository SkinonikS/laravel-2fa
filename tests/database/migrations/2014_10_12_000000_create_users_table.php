<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 
     */
    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nickname', 24)->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->text('otp_secret')->nullable();
            $table->text('recovery_codes')->nullable();
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * 
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
