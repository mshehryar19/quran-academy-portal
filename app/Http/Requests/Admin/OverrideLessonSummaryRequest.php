<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OverrideLessonSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('Admin');
    }

    public function rules(): array
    {
        return [
            'lesson_topic' => ['nullable', 'string', 'max:255'],
            'surah_or_lesson' => ['nullable', 'string', 'max:255'],
            'memorization_progress' => ['nullable', 'string', 'max:5000'],
            'performance_notes' => ['nullable', 'string', 'max:5000'],
            'homework_assigned' => ['nullable', 'string', 'max:5000'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
