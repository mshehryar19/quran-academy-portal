<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ClassSession extends Model
{
    use LogsActivity;

    protected $fillable = [
        'class_schedule_id',
        'session_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
        ];
    }

    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    public function studentAttendance(): HasOne
    {
        return $this->hasOne(StudentClassAttendance::class);
    }

    public function lessonSummary(): HasOne
    {
        return $this->hasOne(LessonSummary::class);
    }

    public function homeworkTasks(): HasMany
    {
        return $this->hasMany(HomeworkTask::class);
    }

    public function progressNotes(): HasMany
    {
        return $this->hasMany(StudentProgressNote::class);
    }

    public function employeeAttendanceEvents(): HasMany
    {
        return $this->hasMany(EmployeeAttendanceEvent::class);
    }

    /** Slot start as Carbon on session day (app timezone). */
    public function slotStartsAt(): Carbon
    {
        $this->loadMissing('classSchedule.classSlot');
        $slot = $this->classSchedule->classSlot;
        $time = substr((string) $slot->start_time, 0, 8);

        return Carbon::parse($this->session_date->toDateString().' '.$time);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Class session {$eventName}");
    }
}
