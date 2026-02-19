<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'beneficiary_id',
        'transfer_type',
        'amount',
        'from_currency',
        'to_currency',
        'exchange_rate',
        'converted_amount',
        'fee',
        'reference_number',
        'description',
        'status',
        'scheduled_date',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'converted_amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'scheduled_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
