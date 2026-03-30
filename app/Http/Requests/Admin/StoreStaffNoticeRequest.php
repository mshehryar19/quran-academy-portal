<?php

namespace App\Http\Requests\Admin;

use App\Models\StaffNotice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStaffNoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('notifications.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'short_alert' => ['required', 'string', 'max:500'],
            'full_message' => ['required', 'string'],
            'category' => ['required', Rule::in([
                StaffNotice::CATEGORY_POLICY,
                StaffNotice::CATEGORY_VIOLATION,
                StaffNotice::CATEGORY_WARNING,
                StaffNotice::CATEGORY_REMINDER,
                StaffNotice::CATEGORY_OPERATIONAL_ALERT,
            ])],
            'severity' => ['nullable', 'string', 'max:32'],
            'recipient_mode' => ['required', Rule::in([
                StaffNotice::MODE_ALL_STAFF,
                StaffNotice::MODE_ROLES,
                StaffNotice::MODE_USERS,
            ])],
            'channels' => ['required', 'array', 'min:1'],
            'channels.*' => ['required', Rule::in(['portal', 'email', 'whatsapp'])],
            'role_names' => ['required_if:recipient_mode,roles', 'array', 'min:1'],
            'role_names.*' => ['string', 'max:64'],
            'user_ids' => ['required_if:recipient_mode,users', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:now'],
        ];
    }
}
