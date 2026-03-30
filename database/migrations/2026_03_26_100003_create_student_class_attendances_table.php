<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_class_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();
            $table->string('status', 16);
            $table->foreignId('marked_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('marked_at');
            $table->boolean('teacher_available_for_reassignment')->default(false);
            $table->timestamps();

            $table->unique('class_session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_class_attendances');
    }
};
