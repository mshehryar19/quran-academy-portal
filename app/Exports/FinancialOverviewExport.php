<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FinancialOverviewExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private readonly ?int $studentId,
        private readonly ?string $status,
        private readonly ?int $billingYear,
        private readonly ?int $billingMonth,
    ) {}

    public function query(): Builder
    {
        $query = Invoice::query()
            ->with(['student'])
            ->orderByDesc('billing_year')
            ->orderByDesc('billing_month');

        if ($this->studentId) {
            $query->where('student_id', $this->studentId);
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }
        if ($this->billingYear) {
            $query->where('billing_year', $this->billingYear);
        }
        if ($this->billingMonth) {
            $query->where('billing_month', $this->billingMonth);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Invoice #',
            'Student',
            'Period',
            'Currency',
            'Total',
            'Paid',
            'Balance',
            'Status',
            'Due',
        ];
    }

    /**
     * @param  Invoice  $row
     */
    public function map($row): array
    {
        return [
            $row->invoice_number,
            $row->student?->full_name ?? '—',
            $row->periodLabel(),
            $row->currency,
            $row->total_amount,
            $row->amount_paid,
            $row->balanceFormatted(),
            $row->status,
            $row->due_date?->toDateString() ?? '',
        ];
    }
}
