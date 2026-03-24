<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ClassSchedule extends Model
{
    use LogsActivity;

    protected $fillable = [
        'teacher_id',
        'student_id',
        'class_slot_id',
        'day_of_week',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function classSlot(): BelongsTo
    {
        return $this->belongsTo(ClassSlot::class);
    }

    public function changeLogs(): HasMany
    {
        return $this->hasMany(ScheduleChangeLog::class)->orderByDesc('created_at');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Class schedule {$eventName}");
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function dayName(int $day): string
    {
        return match ($day) {
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
            default => (string) $day,
        };
    }
}
