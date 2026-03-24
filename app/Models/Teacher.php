<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Teacher extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id',
        'public_id',
        'full_name',
        'email',
        'phone',
        'gender',
        'date_of_appointment',
        'status',
        'address_line',
        'country',
        'timezone',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_appointment' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
            ->setDescriptionForEvent(fn (string $eventName) => "Teacher {$eventName}");
    }
}
