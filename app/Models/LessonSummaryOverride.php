<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonSummaryOverride extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'lesson_summary_id',
        'admin_user_id',
        'previous_values',
        'new_values',
        'reason',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'previous_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function lessonSummary(): BelongsTo
    {
        return $this->belongsTo(LessonSummary::class);
    }

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
