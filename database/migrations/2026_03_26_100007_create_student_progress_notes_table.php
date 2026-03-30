<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_progress_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_session_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index(['student_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_progress_notes');
    }
};
