<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    public const METHOD_MANUAL = 'manual';

    public const METHOD_BANK_TRANSFER = 'bank_transfer';

    public const METHOD_CARD = 'card';

    public const METHOD_CASH = 'cash';

    public const METHOD_ONLINE_PENDING = 'online_pending';

    public const PAYMENT_COMPLETED = 'completed';

    public const PAYMENT_PENDING = 'pending';

    public const PAYMENT_FAILED = 'failed';

    protected $fillable = [
        'invoice_id',
        'student_id',
        'amount',
        'currency',
        'paid_on',
        'method',
        'reference',
        'gateway_transaction_id',
        'payment_status',
        'channel',
        'recorded_by_user_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_on' => 'date',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public static function methods(): array
    {
        return [
            self::METHOD_MANUAL,
            self::METHOD_BANK_TRANSFER,
            self::METHOD_CARD,
            self::METHOD_CASH,
            self::METHOD_ONLINE_PENDING,
        ];
    }
}
