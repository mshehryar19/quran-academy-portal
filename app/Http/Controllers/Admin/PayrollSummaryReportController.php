<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlySalaryRecord;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PayrollSummaryReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:reports.view', 'can:salary.manage']);
    }

    public function index(Request $request)
    {
        $query = MonthlySalaryRecord::query()
            ->with(['user', 'lastComputedBy'])
            ->orderByDesc('period_year')
            ->orderByDesc('period_month');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }
        if ($request->filled('period_year')) {
            $query->where('period_year', $request->integer('period_year'));
        }
        if ($request->filled('period_month')) {
            $query->where('period_month', $request->integer('period_month'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $records = $query->paginate(30)->withQueryString();
        $staffUsers = User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['Teacher', 'HR', 'Supervisor', 'Admin']))
            ->orderBy('name')
            ->get();

        return view('admin.reports.payroll-summary', compact('records', 'staffUsers'));
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $this->logExport($request, 'excel');

        $query = MonthlySalaryRecord::query()->with(['user'])->orderByDesc('period_year')->orderByDesc('period_month');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }
        if ($request->filled('period_year')) {
            $query->where('period_year', $request->integer('period_year'));
        }
        if ($request->filled('period_month')) {
            $query->where('period_month', $request->integer('period_month'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $export = new class($query->get()) implements FromCollection, WithHeadings, WithMapping
        {
            public function __construct(private readonly Collection $rows) {}

            public function collection(): Collection
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return [
                    'Employee',
                    'Period',
                    'Base PKR',
                    'Final payable PKR',
                    'Status',
                ];
            }

            public function map($row): array
            {
                /** @var MonthlySalaryRecord $row */
                return [
                    $row->user?->name ?? '—',
                    $row->periodLabel(),
                    $row->base_salary_pkr,
                    $row->final_payable_pkr,
                    $row->status,
                ];
            }
        };

        return Excel::download($export, 'payroll-summary.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $this->logExport($request, 'pdf');

        $query = MonthlySalaryRecord::query()->with(['user'])->orderByDesc('period_year')->orderByDesc('period_month');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }
        if ($request->filled('period_year')) {
            $query->where('period_year', $request->integer('period_year'));
        }
        if ($request->filled('period_month')) {
            $query->where('period_month', $request->integer('period_month'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        /** @var Collection<int, MonthlySalaryRecord> $records */
        $records = $query->limit(200)->get();

        $pdf = Pdf::loadView('reports.pdf.payroll-summary', ['records' => $records]);

        return $pdf->download('payroll-summary.pdf');
    }

    private function logExport(Request $request, string $format): void
    {
        activity()
            ->causedBy($request->user())
            ->event('report.export')
            ->withProperties([
                'report' => 'payroll_summary',
                'format' => $format,
                'filters' => $request->only(['user_id', 'period_year', 'period_month', 'status']),
            ])
            ->log('Payroll summary report exported');
    }
}
