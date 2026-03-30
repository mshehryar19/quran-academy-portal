<?php

namespace App\Http\Requests\Billing;

use App\Models\InvoicePayment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoicePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('payment.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'currency' => ['required', Rule::in(['GBP', 'USD'])],
            'paid_on' => ['required', 'date'],
            'method' => ['required', Rule::in(InvoicePayment::methods())],
            'reference' => ['nullable', 'string', 'max:255'],
            'gateway_transaction_id' => ['nullable', 'string', 'max:255'],
            'channel' => ['nullable', 'string', 'max:24'],
            'payment_status' => ['nullable', Rule::in([
                InvoicePayment::PAYMENT_COMPLETED,
                InvoicePayment::PAYMENT_PENDING,
                InvoicePayment::PAYMENT_FAILED,
            ])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
