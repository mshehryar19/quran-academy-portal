<?php

namespace App\Http\Requests\Billing;

use App\Models\StudentFeeProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentFeeProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', StudentFeeProfile::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'monthly_fee_amount' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'currency' => ['required', Rule::in(StudentFeeProfile::allowedCurrencies())],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'status' => ['required', Rule::in([StudentFeeProfile::STATUS_ACTIVE, StudentFeeProfile::STATUS_INACTIVE])],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
