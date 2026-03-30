<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StudentClassAttendance extends Model
{
    use LogsActivity;

    protected $fillable = [
        'class_session_id',
        'status',
        'marked_by_user_id',
        'marked_at',
        'teacher_available_for_reassignment',
    ];

    protected function casts(): array
    {
        return [
            'marked_at' => 'datetime',
            'teacher_available_for_reassignment' => 'boolean',
        ];
    }

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by_user_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Student class attendance {$eventName}");
    }
}
