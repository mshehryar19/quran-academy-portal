<?php

namespace App\Http\Requests\Leave;

use App\Models\LeaveRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupervisorLeaveDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('supervisorDecide', $this->route('leave_request')) ?? false;
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::in([LeaveRequest::DECISION_APPROVED, LeaveRequest::DECISION_REJECTED])],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
