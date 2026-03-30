<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalaryProfile extends Model
{
    protected $fillable = [
        'user_id',
        'base_salary_pkr',
        'effective_from',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'base_salary_pkr' => 'decimal:2',
            'effective_from' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
