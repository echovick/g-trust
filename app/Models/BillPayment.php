<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillPayment extends Model
{
    protected $fillable = [
        'account_id',
        'payee_name',
        'payee_account_number',
        'payee_type',
        'amount',
        'currency',
        'reference_number',
        'memo',
        'status',
        'frequency',
        'scheduled_date',
        'next_payment_date',
        'completed_at',
        'auto_pay',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'scheduled_date' => 'datetime',
        'next_payment_date' => 'datetime',
        'completed_at' => 'datetime',
        'auto_pay' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
