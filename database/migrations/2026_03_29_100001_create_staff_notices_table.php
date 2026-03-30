<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_notices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('short_alert', 500);
            $table->text('full_message');
            $table->string('category', 48)->default('operational_alert');
            $table->string('severity', 32)->nullable();
            $table->string('recipient_mode', 24);
            $table->json('channels');
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('staff_notice_target_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_notice_id')->constrained('staff_notices')->cascadeOnDelete();
            $table->string('role_name', 64);
            $table->unique(['staff_notice_id', 'role_name']);
        });

        Schema::create('staff_notice_target_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_notice_id')->constrained('staff_notices')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['staff_notice_id', 'user_id']);
        });

        Schema::create('staff_notice_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_notice_id')->constrained('staff_notices')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->unique(['staff_notice_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_notice_reads');
        Schema::dropIfExists('staff_notice_target_users');
        Schema::dropIfExists('staff_notice_target_roles');
        Schema::dropIfExists('staff_notices');
    }
};
