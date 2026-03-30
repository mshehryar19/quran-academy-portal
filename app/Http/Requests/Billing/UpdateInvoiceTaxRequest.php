<?php

namespace App\Http\Requests\Billing;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceTaxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manageInternally', $this->route('invoice')) ?? false;
    }

    public function rules(): array
    {
        return [
            'tax_amount' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'tax_detail' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
