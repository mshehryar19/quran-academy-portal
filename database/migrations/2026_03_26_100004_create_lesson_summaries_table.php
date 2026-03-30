<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('lesson_topic')->nullable();
            $table->string('surah_or_lesson')->nullable();
            $table->text('memorization_progress')->nullable();
            $table->text('performance_notes')->nullable();
            $table->text('homework_assigned')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();

            $table->unique('class_session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_summaries');
    }
};
