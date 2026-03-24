<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('public_id', 32)->unique()->comment('TCH-0001 format');
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('gender', 16)->nullable();
            $table->date('date_of_appointment')->nullable();
            $table->string('status', 16)->default('active')->index();
            $table->string('address_line')->nullable();
            $table->string('country', 64)->nullable();
            $table->string('timezone', 64)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
