<?php

namespace App\Http\Requests\Leave;

use App\Models\LeaveRequest;
use App\Services\LeaveBalanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', LeaveRequest::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'leave_type' => ['required', Rule::in(LeaveRequest::types())],
            'is_paid' => ['required', 'boolean'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:5000'],
            'attachment' => [
                'nullable',
                'file',
                'max:5120',
                'mimes:pdf,jpg,jpeg,png',
                'mimetypes:application/pdf,image/jpeg,image/png',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->hasFile('attachment')) {
                $mime = $this->file('attachment')->getMimeType();
                $allowed = ['application/pdf', 'image/jpeg', 'image/png'];
                if (! in_array($mime, $allowed, true)) {
                    $validator->errors()->add(
                        'attachment',
                        __('The attachment type was rejected. Upload a PDF or JPEG/PNG file.')
                    );
                }
            }

            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $type = $this->string('leave_type')->toString();
            $isPaid = $this->boolean('is_paid');

            if ($type === LeaveRequest::TYPE_UNPAID && $isPaid) {
                $validator->errors()->add('is_paid', __('Unpaid leave type must be marked as unpaid (not paid).'));

                return;
            }

            if ($type === LeaveRequest::TYPE_MEDICAL && ! $this->hasFile('attachment')) {
                $validator->errors()->add('attachment', __('A medical attachment is required for medical leave.'));

                return;
            }

            $start = Carbon::parse($this->string('start_date')->toString())->startOfDay();
            $end = Carbon::parse($this->string('end_date')->toString())->startOfDay();
            $days = LeaveBalanceService::inclusiveDayCount($start, $end);

            if ($isPaid) {
                $balance = app(LeaveBalanceService::class)->remainingPaidLeaveDays($this->user(), now());
                if ($days > $balance) {
                    $validator->errors()->add(
                        'start_date',
                        __('Not enough paid leave balance (:remaining days remaining).', ['remaining' => $balance])
                    );
                }
            }
        });
    }
}
