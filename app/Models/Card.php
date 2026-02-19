<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Card extends Model
{
    protected $fillable = [
        'account_id',
        'card_number',
        'card_holder_name',
        'card_type',
        'card_brand',
        'expiry_month',
        'expiry_year',
        'cvv',
        'status',
        'credit_limit',
        'available_credit',
        'daily_limit',
        'contactless_enabled',
        'online_transactions_enabled',
        'international_transactions_enabled',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'available_credit' => 'decimal:2',
        'daily_limit' => 'decimal:2',
        'contactless_enabled' => 'boolean',
        'online_transactions_enabled' => 'boolean',
        'international_transactions_enabled' => 'boolean',
    ];

    protected $hidden = [
        'cvv',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
