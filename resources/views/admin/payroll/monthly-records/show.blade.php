@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <h2 class="text-xl font-semibold">Salary {{ $monthlySalaryRecord->periodLabel() }} — {{ $monthlySalaryRecord->user->name }}</h2>
            <p class="text-sm text-gray-600">Status: {{ $monthlySalaryRecord->status }}</p>
        </div>
        <a href="{{ route('admin.monthly-salary-records.index') }}" class="text-sm underline">Back</a>
    </div>

    <dl class="mb-6 grid max-w-xl grid-cols-2 gap-3 rounded-lg border border-gray-200 bg-white p-6 text-sm shadow-sm">
        <dt class="text-gray-500">Base</dt>
        <dd class="text-right">{{ number_format($monthlySalaryRecord->base_salary_pkr, 2) }} PKR</dd>
        <dt class="text-gray-500">Late minutes</dt>
        <dd class="text-right">{{ $monthlySalaryRecord->total_late_minutes }}</dd>
        <dt class="text-gray-500">Late deduction</dt>
        <dd class="text-right">− {{ number_format($monthlySalaryRecord->late_deduction_pkr, 2) }}</dd>
        <dt class="text-gray-500">Unpaid leave days</dt>
        <dd class="text-right">{{ $monthlySalaryRecord->unpaid_leave_days_in_period }}</dd>
        <dt class="text-gray-500">Leave deduction</dt>
        <dd class="text-right">− {{ number_format($monthlySalaryRecord->leave_deduction_pkr, 2) }}</dd>
        <dt class="text-gray-500">Advance deduction</dt>
        <dd class="text-right">− {{ number_format($monthlySalaryRecord->advance_deduction_pkr, 2) }}</dd>
        <dt class="text-gray-500 font-semibold">Final payable</dt>
        <dd class="text-right font-semibold">{{ number_format($monthlySalaryRecord->final_payable_pkr, 2) }} PKR</dd>
    </dl>

    @if($monthlySalaryRecord->calculation_notes)
        <p class="mb-6 text-xs text-gray-600">{{ $monthlySalaryRecord->calculation_notes }}</p>
    @endif

    @if($monthlySalaryRecord->status === \App\Models\MonthlySalaryRecord::STATUS_DRAFT)
        <form method="post" action="{{ route('admin.monthly-salary-records.finalize', $monthlySalaryRecord) }}" onsubmit="return confirm('Finalize this month? Approved advances for this period will be marked deducted.');">
            @csrf
            <button type="submit" class="rounded-md bg-green-800 px-4 py-2 text-sm text-white">Finalize payroll</button>
        </form>
    @endif
@endsection
