<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_attendance_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type', 16)->index();
            $table->timestamp('occurred_at');
            $table->date('attendance_date')->index();
            $table->unsignedSmallInteger('late_minutes')->nullable();
            $table->unsignedBigInteger('paired_login_event_id')->nullable()->index();
            $table->timestamps();

            $table->index(['teacher_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_attendance_events');
    }
};
