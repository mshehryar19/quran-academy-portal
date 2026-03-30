<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffNoticeTargetRole extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'staff_notice_id',
        'role_name',
    ];

    public function staffNotice(): BelongsTo
    {
        return $this->belongsTo(StaffNotice::class);
    }
}
