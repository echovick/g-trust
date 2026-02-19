<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    protected $fillable = [
        'account_id',
        'deposit_type',
        'amount',
        'currency',
        'reference_number',
        'check_number',
        'check_front_image',
        'check_back_image',
        'memo',
        'status',
        'deposit_date',
        'available_date',
        'rejection_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deposit_date' => 'datetime',
        'available_date' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
