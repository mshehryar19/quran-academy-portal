<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    public const TYPE_MEDICAL = 'medical';

    public const TYPE_CASUAL = 'casual';

    public const TYPE_EMERGENCY = 'emergency';

    public const TYPE_UNPAID = 'unpaid';

    public const DECISION_APPROVED = 'approved';

    public const DECISION_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'leave_type',
        'is_paid',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'attachment_path',
        'supervisor_decision',
        'supervisor_user_id',
        'supervisor_comment',
        'supervisor_decided_at',
        'admin_decision',
        'admin_user_id',
        'admin_comment',
        'admin_decided_at',
    ];

    protected function casts(): array
    {
        return [
            'is_paid' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
            'supervisor_decided_at' => 'datetime',
            'admin_decided_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supervisorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_user_id');
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /** Admin decision is the sole determinant of the final outcome once set. */
    public function finalStatus(): ?string
    {
        if ($this->admin_decision === null) {
            return null;
        }

        return $this->admin_decision;
    }

    public function awaitingSupervisor(): bool
    {
        return $this->supervisor_decision === null;
    }

    public function awaitingAdmin(): bool
    {
        return $this->supervisor_decision !== null && $this->admin_decision === null;
    }

    public function isFullyResolved(): bool
    {
        return $this->admin_decision !== null;
    }

    public static function types(): array
    {
        return [
            self::TYPE_MEDICAL,
            self::TYPE_CASUAL,
            self::TYPE_EMERGENCY,
            self::TYPE_UNPAID,
        ];
    }
}
