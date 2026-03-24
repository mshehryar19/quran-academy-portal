<?php

namespace App\Http\Requests\Admin;

use App\Models\ClassSchedule;
use App\Models\ClassSlot;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\ScheduleConflictService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ClassSchedule::class);
    }

    public function rules(): array
    {
        return [
            'teacher_id' => ['required', 'exists:teachers,id'],
            'student_id' => ['required', 'exists:students,id'],
            'class_slot_id' => ['required', 'exists:class_slots,id'],
            'day_of_week' => ['required', 'integer', 'min:1', 'max:7'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $teacher = Teacher::query()->with('user')->find($this->integer('teacher_id'));
            $student = Student::query()->with('user')->find($this->integer('student_id'));
            $slot = ClassSlot::query()->find($this->integer('class_slot_id'));

            if (! $teacher || ! $student || ! $slot) {
                return;
            }

            if ($this->string('status')->toString() === 'active') {
                if ($teacher->status !== 'active' || ! $teacher->user?->is_active) {
                    $validator->errors()->add('teacher_id', __('Cannot assign an inactive teacher to an active schedule.'));
                }
                if ($student->status !== 'active' || ! $student->user?->is_active) {
                    $validator->errors()->add('student_id', __('Cannot assign an inactive student to an active schedule.'));
                }
                if ($slot->status !== 'active') {
                    $validator->errors()->add('class_slot_id', __('Cannot use an inactive class slot for an active schedule.'));
                }
            }

            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if ($this->string('status')->toString() !== 'active') {
                return;
            }

            $conflict = app(ScheduleConflictService::class)->validateNewOrUpdate(
                $teacher->id,
                $student->id,
                $slot->id,
                $this->integer('day_of_week'),
                $this->date('start_date')->format('Y-m-d'),
                $this->filled('end_date') ? $this->date('end_date')->format('Y-m-d') : null,
                null,
            );

            if ($conflict) {
                $validator->errors()->add('class_slot_id', $conflict);
            }
        });
    }
}
