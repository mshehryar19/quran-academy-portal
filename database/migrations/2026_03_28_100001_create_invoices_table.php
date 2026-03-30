<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_fee_profile_id')->nullable()->constrained('student_fee_profiles')->nullOnDelete();
            $table->string('invoice_number', 32)->unique();
            $table->unsignedSmallInteger('billing_year');
            $table->unsignedTinyInteger('billing_month');
            $table->string('currency', 3);
            $table->decimal('tuition_amount', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->json('tax_detail')->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('status', 24)->default('unpaid');
            $table->text('notes')->nullable();
            $table->text('void_reason')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->string('gateway_reference')->nullable();
            $table->string('billing_source', 24)->default('internal');
            $table->timestamp('issued_at')->nullable();
            $table->foreignId('generated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['student_id', 'billing_year', 'billing_month']);
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
