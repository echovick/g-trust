<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    protected $fillable = [
        'account_id',
        'transaction_type',
        'category',
        'description',
        'amount',
        'currency',
        'balance_after',
        'reference_number',
        'status',
        'merchant_name',
        'merchant_category',
        'related_account_id',
        'transaction_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function relatedAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'related_account_id');
    }

    public function transfer(): HasOne
    {
        return $this->hasOne(Transfer::class, 'reference_number', 'reference_number');
    }
}
