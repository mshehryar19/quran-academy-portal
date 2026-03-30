<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgressNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageAsTeacher', $this->route('class_session'));
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
        ];
    }
}
