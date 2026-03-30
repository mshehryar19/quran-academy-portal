<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advance_salary_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_pkr', 14, 2);
            $table->text('reason')->nullable();
            $table->string('status', 24)->default('pending');
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_comment')->nullable();
            $table->timestamp('admin_decided_at')->nullable();
            $table->unsignedSmallInteger('deduction_period_year')->nullable();
            $table->unsignedTinyInteger('deduction_period_month')->nullable();
            $table->timestamps();

            $table->index(['status', 'deduction_period_year', 'deduction_period_month'], 'adv_salary_sched_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advance_salary_requests');
    }
};
