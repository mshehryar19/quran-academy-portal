@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">My salary (PKR)</h2>
        <p class="text-sm text-gray-600">Finalized months only — contact admin if a period is missing.</p>
    </div>

    @if($records->isEmpty())
        <p class="rounded-lg border border-gray-200 bg-white p-6 text-sm text-gray-600 shadow-sm">No finalized salary records yet.</p>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full text-sm">
                <thead>
                <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                    <th class="px-3 py-2">Period</th>
                    <th class="px-3 py-2 text-right">Base (PKR)</th>
                    <th class="px-3 py-2 text-right">Late −</th>
                    <th class="px-3 py-2 text-right">Leave −</th>
                    <th class="px-3 py-2 text-right">Advance −</th>
                    <th class="px-3 py-2 text-right">Payable (PKR)</th>
                    <th class="px-3 py-2"></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($records as $r)
                    <tr class="border-b border-gray-100">
                        <td class="px-3 py-2 font-mono">{{ $r->periodLabel() }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($r->base_salary_pkr, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($r->late_deduction_pkr, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($r->leave_deduction_pkr, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($r->advance_deduction_pkr, 2) }}</td>
                        <td class="px-3 py-2 text-right font-semibold">{{ number_format($r->final_payable_pkr, 2) }}</td>
                        <td class="px-3 py-2">
@if($r->status === \App\Models\MonthlySalaryRecord::STATUS_FINALIZED)
                            <a href="{{ route('employee.salary.show', $r) }}" class="underline">Detail</a>
@endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $records->links() }}</div>
    @endif
@endsection
