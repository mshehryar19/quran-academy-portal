<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_salary_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->decimal('base_salary_pkr', 14, 2);
            $table->unsignedInteger('total_late_minutes')->default(0);
            $table->decimal('late_deduction_pkr', 14, 2)->default(0);
            $table->decimal('leave_deduction_pkr', 14, 2)->default(0);
            $table->unsignedSmallInteger('unpaid_leave_days_in_period')->default(0);
            $table->decimal('advance_deduction_pkr', 14, 2)->default(0);
            $table->decimal('other_adjustment_pkr', 14, 2)->default(0);
            $table->decimal('final_payable_pkr', 14, 2);
            $table->string('status', 24)->default('draft');
            $table->text('calculation_notes')->nullable();
            $table->foreignId('last_computed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_computed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'period_year', 'period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_salary_records');
    }
};
