<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHomeworkTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('homework_task');
        $task->loadMissing('classSession');

        return $this->user()->can('manageAsTeacher', $task->classSession);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['pending', 'completed', 'not_completed'])],
        ];
    }
}
