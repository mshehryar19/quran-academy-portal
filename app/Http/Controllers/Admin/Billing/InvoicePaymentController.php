<?php

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\StoreInvoicePaymentRequest;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Services\InvoiceTotalsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class InvoicePaymentController extends Controller
{
    public function store(StoreInvoicePaymentRequest $request, Invoice $invoice, InvoiceTotalsService $totals): RedirectResponse
    {
        abort_unless($request->user()->can('invoice.manage'), 403);

        $amount = number_format((float) $request->input('amount'), 2, '.', '');
        $currency = $request->string('currency')->toString();

        $totals->assertPaymentFits($invoice, $amount, $currency);

        DB::transaction(function () use ($request, $invoice, $amount, $currency, $totals): void {
            InvoicePayment::query()->create([
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'amount' => $amount,
                'currency' => $currency,
                'paid_on' => $request->date('paid_on')->toDateString(),
                'method' => $request->string('method')->toString(),
                'reference' => $request->input('reference'),
                'gateway_transaction_id' => $request->input('gateway_transaction_id'),
                'payment_status' => $request->input('payment_status', InvoicePayment::PAYMENT_COMPLETED),
                'channel' => $request->input('channel', 'manual'),
                'recorded_by_user_id' => $request->user()->id,
                'notes' => $request->input('notes'),
            ]);

            $invoice->refresh();
            $totals->refresh($invoice);
        });

        activity()
            ->performedOn($invoice)
            ->causedBy($request->user())
            ->event('billing.payment_recorded')
            ->log('Invoice payment recorded');

        return redirect()->route('admin.billing.invoices.show', $invoice)->with('status', __('Payment recorded.'));
    }
}
