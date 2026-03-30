<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentBillingController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user()->student;
        abort_if(! $student, 404);

        $invoices = Invoice::query()
            ->where('student_id', $student->id)
            ->where('status', '!=', Invoice::STATUS_CANCELLED)
            ->orderByDesc('billing_year')
            ->orderByDesc('billing_month')
            ->paginate(15);

        return view('portal.student-billing.index', compact('student', 'invoices'));
    }

    public function show(Request $request, Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $invoice->load(['student', 'payments']);

        return view('portal.student-billing.show', compact('invoice'));
    }
}
