<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageAsTeacher', $this->route('class_session'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['present', 'absent'])],
            'teacher_available_for_reassignment' => ['sometimes', 'boolean'],
        ];
    }
}
