<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffNoticeTargetUser extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'staff_notice_id',
        'user_id',
    ];

    public function staffNotice(): BelongsTo
    {
        return $this->belongsTo(StaffNotice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
