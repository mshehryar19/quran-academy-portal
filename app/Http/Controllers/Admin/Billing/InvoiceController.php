<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\GenerateInvoicesRequest;
use App\Http\Requests\Billing\UpdateInvoiceTaxRequest;
use App\Http\Requests\Billing\VoidInvoiceRequest;
use App\Models\Invoice;
use App\Models\Student;
use App\Services\InvoiceGenerationService;
use App\Services\InvoiceTotalsService;
use App\Services\SystemNotificationDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('invoice.manage'), 403);

        $query = Invoice::query()
            ->with(['student', 'feeProfile'])
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

        $invoices = $query->paginate(30)->withQueryString();
        $students = Student::query()->orderBy('full_name')->get();

        return view('admin.billing.invoices.index', compact('invoices', 'students'));
    }

    public function show(Invoice $invoice): View
    {
        abort_unless(request()->user()?->can('manageInternally', $invoice), 403);
        $invoice->load(['student', 'feeProfile', 'payments.recordedBy', 'generatedBy']);

        return view('admin.billing.invoices.show', compact('invoice'));
    }

    public function generateForm(): View
    {
        abort_unless(auth()->user()?->can('invoice.manage'), 403);
        $students = Student::query()->orderBy('full_name')->get();

        return view('admin.billing.invoices.generate', compact('students'));
    }

    public function generateRun(GenerateInvoicesRequest $request, InvoiceGenerationService $generator, SystemNotificationDispatcher $notifications): RedirectResponse
    {
        $only = $request->filled('student_ids') ? $request->input('student_ids') : null;

        $created = $generator->generateForBillingMonth(
            $request->integer('billing_year'),
            $request->integer('billing_month'),
            $request->user()->id,
            $only
        );

        foreach ($created as $invoice) {
            activity()
                ->performedOn($invoice)
                ->causedBy($request->user())
                ->event('billing.invoice_generated')
                ->log('Invoice / voucher generated');

            $notifications->invoiceGenerated($invoice);
        }

        return redirect()->route('admin.billing.invoices.index')->with('status', __('Generated :count invoice(s).', ['count' => $created->count()]));
    }

    public function void(VoidInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        if ((float) $invoice->amount_paid > 0) {
            return back()->withErrors(['void' => __('Cannot void an invoice with recorded payments.')]);
        }

        $invoice->update([
            'status' => Invoice::STATUS_CANCELLED,
            'void_reason' => $request->string('void_reason')->toString(),
            'voided_at' => now(),
        ]);

        activity()
            ->performedOn($invoice)
            ->causedBy($request->user())
            ->event('billing.invoice_voided')
            ->log('Invoice voided');

        return redirect()->route('admin.billing.invoices.show', $invoice)->with('status', __('Invoice voided.'));
    }

    public function updateTax(UpdateInvoiceTaxRequest $request, Invoice $invoice, InvoiceTotalsService $totals): RedirectResponse
    {
        if ($invoice->status === Invoice::STATUS_CANCELLED) {
            return back()->withErrors(['invoice' => __('Cancelled invoice cannot be edited.')]);
        }

        $tuition = (string) $invoice->tuition_amount;
        $tax = number_format((float) $request->input('tax_amount'), 2, '.', '');
        $total = bcadd($tuition, $tax, 2);

        if ((float) $invoice->amount_paid > (float) $total) {
            return back()->withErrors(['tax_amount' => __('Total would be less than amount already paid.')]);
        }

        $invoice->update([
            'tax_amount' => $tax,
            'tax_detail' => $request->input('tax_detail'),
            'total_amount' => $total,
            'notes' => $request->input('notes') ?? $invoice->notes,
        ]);

        $totals->refresh($invoice);

        activity()
            ->performedOn($invoice)
            ->causedBy($request->user())
            ->event('billing.invoice_tax_updated')
            ->log('Invoice tax / total updated');

        return redirect()->route('admin.billing.invoices.show', $invoice)->with('status', __('Invoice totals updated.'));
    }
}
