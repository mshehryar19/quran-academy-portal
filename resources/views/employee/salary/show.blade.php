@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Salary {{ $monthlySalaryRecord->periodLabel() }} (PKR)</h2>
        <p class="text-sm text-gray-600">View only</p>
    </div>

    <dl class="grid max-w-lg grid-cols-1 gap-3 rounded-lg border border-gray-200 bg-white p-6 text-sm shadow-sm sm:grid-cols-2">
        <dt class="text-gray-500">Base salary</dt>
        <dd class="text-right font-medium">{{ number_format($monthlySalaryRecord->base_salary_pkr, 2) }} PKR</dd>
        <dt class="text-gray-500">Late minutes (period)</dt>
        <dd class="text-right">{{ $monthlySalaryRecord->total_late_minutes }}</dd>
        <dt class="text-gray-500">Late deduction</dt>
        <dd class="text-right text-red-700">− {{ number_format($monthlySalaryRecord->late_deduction_pkr, 2) }}</dd>
        <dt class="text-gray-500">Unpaid leave days</dt>
        <dd class="text-right">{{ $monthlySalaryRecord->unpaid_leave_days_in_period }}</dd>
        <dt class="text-gray-500">Leave deduction</dt>
        <dd class="text-right text-red-700">− {{ number_format($monthlySalaryRecord->leave_deduction_pkr, 2) }}</dd>
        <dt class="text-gray-500">Advance deduction</dt>
        <dd class="text-right text-red-700">− {{ number_format($monthlySalaryRecord->advance_deduction_pkr, 2) }}</dd>
        <dt class="text-gray-500 font-semibold">Final payable</dt>
        <dd class="text-right text-lg font-semibold">{{ number_format($monthlySalaryRecord->final_payable_pkr, 2) }} PKR</dd>
    </dl>
    @if($monthlySalaryRecord->calculation_notes)
        <p class="mt-4 max-w-lg text-xs text-gray-500">{{ $monthlySalaryRecord->calculation_notes }}</p>
    @endif
    <p class="mt-6"><a href="{{ route('employee.salary.index') }}" class="text-sm underline">Back</a></p>
@endsection
