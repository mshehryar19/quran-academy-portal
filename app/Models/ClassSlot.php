<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ClassSlot extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'duration_minutes',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
        ];
    }

    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Class slot {$eventName}");
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /** Display label e.g. "17:00–17:30" */
    public function timeRangeLabel(): string
    {
        $s = substr((string) $this->start_time, 0, 5);
        $e = substr((string) $this->end_time, 0, 5);

        return "{$s}–{$e}";
    }
}
