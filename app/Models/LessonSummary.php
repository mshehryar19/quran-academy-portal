<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LessonSummary extends Model
{
    use LogsActivity;

    protected $fillable = [
        'class_session_id',
        'teacher_id',
        'student_id',
        'lesson_topic',
        'surah_or_lesson',
        'memorization_progress',
        'performance_notes',
        'homework_assigned',
        'submitted_at',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'locked_at' => 'datetime',
        ];
    }

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function overrides(): HasMany
    {
        return $this->hasMany(LessonSummaryOverride::class);
    }

    public function homeworkTasks(): HasMany
    {
        return $this->hasMany(HomeworkTask::class);
    }

    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    public function submissionDeadlineEndsAt(): Carbon
    {
        $this->loadMissing('classSession');

        return $this->classSession->session_date->copy()->addDay()->endOfDay();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Lesson summary {$eventName}");
    }
}
