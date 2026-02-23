<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardRequest extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'card_type',
        'card_brand',
        'purpose',
        'status',
        'approved_by',
        'rejection_reason',
        'approved_at',
        'rejected_at',
        'created_card_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdCard(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'created_card_id');
    }
}
