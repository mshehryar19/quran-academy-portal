<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecomputeMonthlySalaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('salary.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', Rule::exists('employee_salary_profiles', 'user_id')],
            'period_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'period_month' => ['required', 'integer', 'min:1', 'max:12'],
        ];
    }
}
