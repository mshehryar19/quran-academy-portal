<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmployeeAttendanceEvent extends Model
{
    protected $fillable = [
        'teacher_id',
        'class_session_id',
        'event_type',
        'occurred_at',
        'attendance_date',
        'late_minutes',
        'paired_login_event_id',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'attendance_date' => 'date',
            'late_minutes' => 'integer',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function pairedLogin(): BelongsTo
    {
        return $this->belongsTo(self::class, 'paired_login_event_id');
    }

    /** Logout row that closes this login event (paired_login_event_id → this login id). */
    public function pairedLogout(): HasOne
    {
        return $this->hasOne(self::class, 'paired_login_event_id')
            ->where('event_type', 'logout');
    }
}
