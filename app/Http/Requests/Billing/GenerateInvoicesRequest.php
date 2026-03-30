<?php

namespace App\Http\Requests\Billing;

use Illuminate\Foundation\Http\FormRequest;

class GenerateInvoicesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('invoice.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'billing_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'billing_month' => ['required', 'integer', 'min:1', 'max:12'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ];
    }
}
