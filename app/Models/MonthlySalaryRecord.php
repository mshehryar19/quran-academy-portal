<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlySalaryRecord extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_FINALIZED = 'finalized';

    protected $fillable = [
        'user_id',
        'period_year',
        'period_month',
        'base_salary_pkr',
        'total_late_minutes',
        'late_deduction_pkr',
        'leave_deduction_pkr',
        'unpaid_leave_days_in_period',
        'advance_deduction_pkr',
        'other_adjustment_pkr',
        'final_payable_pkr',
        'status',
        'calculation_notes',
        'last_computed_by_user_id',
        'last_computed_at',
    ];

    protected function casts(): array
    {
        return [
            'base_salary_pkr' => 'decimal:2',
            'late_deduction_pkr' => 'decimal:2',
            'leave_deduction_pkr' => 'decimal:2',
            'advance_deduction_pkr' => 'decimal:2',
            'other_adjustment_pkr' => 'decimal:2',
            'final_payable_pkr' => 'decimal:2',
            'last_computed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lastComputedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_computed_by_user_id');
    }

    public function periodLabel(): string
    {
        return sprintf('%04d-%02d', $this->period_year, $this->period_month);
    }
}
