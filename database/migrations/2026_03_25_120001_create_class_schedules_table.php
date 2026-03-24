<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_slot_id')->constrained('class_slots')->restrictOnDelete();
            /** ISO day: 1 = Monday … 7 = Sunday */
            $table->unsignedTinyInteger('day_of_week');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status', 16)->default('active')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['teacher_id', 'day_of_week', 'class_slot_id']);
            $table->index(['student_id', 'day_of_week', 'class_slot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
