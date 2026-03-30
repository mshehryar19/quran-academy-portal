<?php

namespace App\Http\Requests\Teacher;

use App\Models\LessonSummary;
use Illuminate\Foundation\Http\FormRequest;

class StoreLessonSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageAsTeacher', $this->route('class_session'));
    }

    public function rules(): array
    {
        return [
            'lesson_topic' => ['nullable', 'string', 'max:255'],
            'surah_or_lesson' => ['nullable', 'string', 'max:255'],
            'memorization_progress' => ['nullable', 'string', 'max:5000'],
            'performance_notes' => ['nullable', 'string', 'max:5000'],
            'homework_assigned' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $session = $this->route('class_session');

            if (LessonSummary::query()->where('class_session_id', $session->id)->exists()) {
                $validator->errors()->add('lesson', __('A lesson summary already exists for this session.'));

                return;
            }

            $deadline = $session->session_date->copy()->addDay()->endOfDay();
            if (now()->gt($deadline)) {
                $validator->errors()->add('lesson', __('The submission window (same day or next calendar day) has closed.'));
            }
        });
    }
}
