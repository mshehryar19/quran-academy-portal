<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    public const STATUS_UNPAID = 'unpaid';

    public const STATUS_PARTIALLY_PAID = 'partially_paid';

    public const STATUS_PAID = 'paid';

    public const STATUS_OVERDUE = 'overdue';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'student_id',
        'student_fee_profile_id',
        'invoice_number',
        'billing_year',
        'billing_month',
        'currency',
        'tuition_amount',
        'tax_amount',
        'tax_detail',
        'total_amount',
        'amount_paid',
        'due_date',
        'status',
        'notes',
        'void_reason',
        'voided_at',
        'gateway_reference',
        'billing_source',
        'issued_at',
        'generated_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'tuition_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'due_date' => 'date',
            'tax_detail' => 'array',
            'voided_at' => 'datetime',
            'issued_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeProfile(): BelongsTo
    {
        return $this->belongsTo(StudentFeeProfile::class, 'student_fee_profile_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by_user_id');
    }

    public function balanceOutstanding(): float
    {
        return max(0, round((float) $this->total_amount - (float) $this->amount_paid, 2));
    }

    public function balanceFormatted(): string
    {
        return number_format($this->balanceOutstanding(), 2, '.', '');
    }

    public function isOverdueFlag(): bool
    {
        if ($this->status === self::STATUS_CANCELLED || $this->status === self::STATUS_PAID) {
            return false;
        }

        if (! $this->due_date) {
            return false;
        }

        return $this->due_date->isPast() && $this->balanceOutstanding() > 0;
    }

    public function periodLabel(): string
    {
        return sprintf('%04d-%02d', $this->billing_year, $this->billing_month);
    }
}
