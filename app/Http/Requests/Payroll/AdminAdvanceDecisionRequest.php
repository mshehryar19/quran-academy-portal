<?php

namespace App\Http\Requests\Payroll;

use App\Models\AdvanceSalaryRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminAdvanceDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('adminReview', AdvanceSalaryRequest::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::in([AdvanceSalaryRequest::STATUS_APPROVED, AdvanceSalaryRequest::STATUS_REJECTED])],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
