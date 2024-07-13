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
        Schema::table('users', function (Blueprint $table) {
            $table->string('fullName')->nullable();
            $table->string('relationship')->nullable();
            $table->boolean('diagnosedForSMA')->default(false);
            $table->boolean('symptoms')->default(false);
            $table->string('typeOfSMA')->nullable();
            $table->string('doctorName')->nullable();
            $table->string('fatherMobile')->nullable();
            $table->string('motherMobile')->nullable();
            $table->string('emergencyContact')->nullable();
            $table->string('presentAddress')->nullable();
            $table->string('permanentAddress')->nullable();
            $table->boolean('agreement')->default(false);
            $table->date('dateOfBirth')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'fullName',
                'relationship',
                'diagnosedForSMA',
                'symptoms',
                'typeOfSMA',
                'doctorName',
                'fatherMobile',
                'motherMobile',
                'emergencyContact',
                'presentAddress',
                'permanentAddress',
                'agreement',
                'dateOfBirth'
            ]);
        });
    }
};
