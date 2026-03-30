<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSystemSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('settings.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'system_name' => ['required', 'string', 'max:120'],
            'default_currency' => ['required', 'string', 'max:12', Rule::in(['GBP', 'USD', 'EUR', 'PKR'])],
            'default_timezone' => ['required', 'string', 'max:64'],
            'invoice_number_prefix' => ['required', 'string', 'max:12', 'regex:/^[A-Za-z0-9]+$/'],
        ];
    }
}
