<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class InvoiceTotalsService
{
    public function refresh(Invoice $invoice): void
    {
        if ($invoice->status === Invoice::STATUS_CANCELLED) {
            return;
        }

        $paid = (string) $invoice->payments()
            ->where('payment_status', InvoicePayment::PAYMENT_COMPLETED)
            ->sum('amount');

        $invoice->amount_paid = $paid;
        $total = (float) $invoice->total_amount;
        $paidFloat = (float) $paid;

        if ($paidFloat <= 0) {
            $invoice->status = Invoice::STATUS_UNPAID;
        } elseif (round($paidFloat, 2) >= round($total, 2)) {
            $invoice->status = Invoice::STATUS_PAID;
        } else {
            $invoice->status = Invoice::STATUS_PARTIALLY_PAID;
        }

        if ($invoice->status !== Invoice::STATUS_PAID
            && $invoice->due_date
            && Carbon::parse($invoice->due_date)->endOfDay()->isPast()
            && (float) $invoice->balanceOutstanding() > 0
        ) {
            $invoice->status = Invoice::STATUS_OVERDUE;
        }

        $invoice->save();
    }

    public function assertPaymentFits(Invoice $invoice, string $amount, string $currency): void
    {
        if ($invoice->status === Invoice::STATUS_CANCELLED) {
            throw ValidationException::withMessages([
                'invoice' => __('Cannot record payment on a cancelled invoice.'),
            ]);
        }

        if ($currency !== $invoice->currency) {
            throw ValidationException::withMessages([
                'currency' => __('Payment currency must match the invoice currency.'),
            ]);
        }

        $currentPaid = (float) $invoice->payments()
            ->where('payment_status', InvoicePayment::PAYMENT_COMPLETED)
            ->sum('amount');
        $total = (float) $invoice->total_amount;
        $next = $currentPaid + (float) $amount;

        if (round($next, 2) > round($total, 2)) {
            throw ValidationException::withMessages([
                'amount' => __('Payment would exceed the invoice total.'),
            ]);
        }
    }
}
