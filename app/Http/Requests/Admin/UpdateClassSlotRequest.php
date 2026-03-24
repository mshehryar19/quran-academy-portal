<?php

namespace App\Http\Requests\Admin;

use App\Models\ClassSlot;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClassSlotRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        foreach (['start_time', 'end_time'] as $field) {
            $v = $this->input($field);
            if (is_string($v) && strlen($v) >= 8 && str_contains($v, ':')) {
                $this->merge([$field => substr($v, 0, 5)]);
            }
        }
    }

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('class_slot'));
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
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
            $start = $this->input('start_time');
            $end = $this->input('end_time');
            $minutes = $this->diffMinutes($start, $end);
            if ($minutes !== 30) {
                $validator->errors()->add('end_time', __('Slots must be exactly 30 minutes (one-to-one class model).'));
            }

            $slot = $this->route('class_slot');
            $normS = Carbon::createFromFormat('H:i', $start)->format('H:i:s');
            $normE = Carbon::createFromFormat('H:i', $end)->format('H:i:s');

            $exists = ClassSlot::query()
                ->where('start_time', $normS)
                ->where('end_time', $normE)
                ->where('id', '!=', $slot->id)
                ->exists();

            if ($exists) {
                $validator->errors()->add('start_time', __('A slot with this start and end time already exists.'));
            }
        });
    }

    private function diffMinutes(string $start, string $end): int
    {
        $s = Carbon::createFromFormat('H:i', $start);
        $e = Carbon::createFromFormat('H:i', $end);

        return (int) $s->diffInMinutes($e);
    }
}
