<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvanceSalaryRequest extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_DEDUCTED = 'deducted';

    protected $fillable = [
        'user_id',
        'amount_pkr',
        'reason',
        'status',
        'admin_user_id',
        'admin_comment',
        'admin_decided_at',
        'deduction_period_year',
        'deduction_period_month',
    ];

    protected function casts(): array
    {
        return [
            'amount_pkr' => 'decimal:2',
            'admin_decided_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
