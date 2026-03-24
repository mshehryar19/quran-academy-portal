<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_slots', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            $table->string('status', 16)->default('active')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['start_time', 'end_time'], 'class_slots_time_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_slots');
    }
};
