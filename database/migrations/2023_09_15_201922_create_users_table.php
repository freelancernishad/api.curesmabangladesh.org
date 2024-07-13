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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile')->unique();
            $table->string('blood_group')->nullable();
            $table->string('email')->unique();
            $table->enum('gander', ['Male', 'Female', 'Other'])->default('Male');
            $table->string('gardiant_phone')->nullable();
            $table->date('last_donate_date')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('division')->nullable();
            $table->string('district')->nullable();
            $table->string('thana')->nullable();
            $table->string('union')->nullable();
            $table->unsignedBigInteger('org')->nullable();
            $table->foreign('org')->references('id')->on('organizations');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
