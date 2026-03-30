<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentBillingController extends Controller
{
    public function index(Request $request): View
    {
        $parent = $request->user()->academyParent;
        abort_if(! $parent, 404);

        $students = $parent->students()->orderBy('full_name')->get();

        return view('portal.parent-billing.index', compact('parent', 'students'));
    }

    public function studentInvoices(Request $request, Student $student): View
    {
        $parent = $request->user()->academyParent;
        abort_if(! $parent, 404);
        abort_unless($parent->students()->where('students.id', $student->id)->exists(), 403);

        $invoices = Invoice::query()
            ->where('student_id', $student->id)
            ->where('status', '!=', Invoice::STATUS_CANCELLED)
            ->orderByDesc('billing_year')
            ->orderByDesc('billing_month')
            ->paginate(15);

        return view('portal.parent-billing.student-invoices', compact('parent', 'student', 'invoices'));
    }

    public function showInvoice(Request $request, Student $student, Invoice $invoice): View
    {
        $parent = $request->user()->academyParent;
        abort_if(! $parent, 404);
        abort_unless($parent->students()->where('students.id', $student->id)->exists(), 403);
        abort_unless((int) $invoice->student_id === (int) $student->id, 404);

        $this->authorize('view', $invoice);

        $invoice->load(['student', 'payments']);

        return view('portal.parent-billing.show-invoice', compact('parent', 'student', 'invoice'));
    }
}
