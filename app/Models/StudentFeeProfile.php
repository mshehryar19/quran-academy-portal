<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentFeeProfile extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const CURRENCY_GBP = 'GBP';

    public const CURRENCY_USD = 'USD';

    protected $fillable = [
        'student_id',
        'monthly_fee_amount',
        'currency',
        'effective_from',
        'effective_to',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'monthly_fee_amount' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public static function allowedCurrencies(): array
    {
        return [self::CURRENCY_GBP, self::CURRENCY_USD];
    }
}
