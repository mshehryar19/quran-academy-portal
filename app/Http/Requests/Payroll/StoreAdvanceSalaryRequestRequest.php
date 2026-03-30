<?php

namespace App\Http\Requests\Payroll;

use App\Models\AdvanceSalaryRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdvanceSalaryRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', AdvanceSalaryRequest::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'amount_pkr' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'reason' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
