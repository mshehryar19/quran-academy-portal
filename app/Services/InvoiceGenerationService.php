<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceGenerationService
{
    public function __construct(
        private readonly StudentFeeProfileResolver $feeResolver,
        private readonly InvoiceNumberService $invoiceNumbers,
    ) {}

    /**
     * @return Collection<int, Invoice>
     */
    public function generateForBillingMonth(int $year, int $month, ?int $actorUserId = null, ?array $onlyStudentIds = null): Collection
    {
        $created = collect();

        $students = Student::query()
            ->when($onlyStudentIds, fn ($q) => $q->whereIn('id', $onlyStudentIds))
            ->orderBy('full_name')
            ->get();

        foreach ($students as $student) {
            if ($this->hasBlockingInvoice($student->id, $year, $month)) {
                continue;
            }

            $profile = $this->feeResolver->activeProfileForMonth($student, $year, $month);
            if (! $profile) {
                continue;
            }

            $invoice = DB::transaction(function () use ($student, $profile, $year, $month, $actorUserId): Invoice {
                $tuition = (string) $profile->monthly_fee_amount;
                $tax = '0.00';
                $total = bcadd($tuition, $tax, 2);

                $monthEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth();

                return Invoice::query()->create([
                    'student_id' => $student->id,
                    'student_fee_profile_id' => $profile->id,
                    'invoice_number' => $this->invoiceNumbers->nextInvoiceNumber($year),
                    'billing_year' => $year,
                    'billing_month' => $month,
                    'currency' => $profile->currency,
                    'tuition_amount' => $tuition,
                    'tax_amount' => $tax,
                    'tax_detail' => null,
                    'total_amount' => $total,
                    'amount_paid' => 0,
                    'due_date' => $monthEnd->copy()->addDays(7)->toDateString(),
                    'status' => Invoice::STATUS_UNPAID,
                    'billing_source' => 'internal',
                    'issued_at' => now(),
                    'generated_by_user_id' => $actorUserId,
                ]);
            });

            $created->push($invoice);
        }

        return $created;
    }

    public function assertNoDuplicate(int $studentId, int $year, int $month): void
    {
        if ($this->hasBlockingInvoice($studentId, $year, $month)) {
            throw ValidationException::withMessages([
                'billing' => __('An invoice already exists for this student and billing month.'),
            ]);
        }
    }

    private function hasBlockingInvoice(int $studentId, int $year, int $month): bool
    {
        return Invoice::query()
            ->where('student_id', $studentId)
            ->where('billing_year', $year)
            ->where('billing_month', $month)
            ->where('status', '!=', Invoice::STATUS_CANCELLED)
            ->exists();
    }
}
