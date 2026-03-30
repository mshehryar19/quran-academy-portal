<?php

namespace App\Http\Controllers\Admin;

use App\Exports\FinancialOverviewExport;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FinancialOverviewReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:reports.view', 'role:Admin|Accountant']);
    }

    public function index(Request $request)
    {
        $query = Invoice::query()
            ->with(['student'])
            ->orderByDesc('billing_year')
            ->orderByDesc('billing_month');

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->integer('student_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('billing_year')) {
            $query->where('billing_year', $request->integer('billing_year'));
        }
        if ($request->filled('billing_month')) {
            $query->where('billing_month', $request->integer('billing_month'));
        }

        $invoices = $query->paginate(35)->withQueryString();
        $students = Student::query()->orderBy('full_name')->get();

        $outstanding = (float) Invoice::query()
            ->where('status', '!=', Invoice::STATUS_CANCELLED)
            ->get()
            ->sum(fn (Invoice $i) => $i->balanceOutstanding());

        return view('admin.reports.financial-overview', compact('invoices', 'students', 'outstanding'));
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $this->logExport($request, 'excel');

        return Excel::download(
            new FinancialOverviewExport(
                $request->filled('student_id') ? $request->integer('student_id') : null,
                $request->filled('status') ? $request->string('status')->toString() : null,
                $request->filled('billing_year') ? $request->integer('billing_year') : null,
                $request->filled('billing_month') ? $request->integer('billing_month') : null,
            ),
            'financial-invoices.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $this->logExport($request, 'pdf');

        $query = Invoice::query()->with(['student'])->orderByDesc('billing_year')->orderByDesc('billing_month');

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->integer('student_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('billing_year')) {
            $query->where('billing_year', $request->integer('billing_year'));
        }
        if ($request->filled('billing_month')) {
            $query->where('billing_month', $request->integer('billing_month'));
        }

        /** @var Collection<int, Invoice> $invoices */
        $invoices = $query->limit(200)->get();

        $pdf = Pdf::loadView('reports.pdf.financial-overview', ['invoices' => $invoices]);

        return $pdf->download('financial-invoices.pdf');
    }

    private function logExport(Request $request, string $format): void
    {
        activity()
            ->causedBy($request->user())
            ->event('report.export')
            ->withProperties([
                'report' => 'financial_overview',
                'format' => $format,
                'filters' => $request->only(['student_id', 'status', 'billing_year', 'billing_month']),
            ])
            ->log('Financial overview report exported');
    }
}
