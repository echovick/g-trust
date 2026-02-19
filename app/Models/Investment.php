<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Investment extends Model
{
    protected $fillable = [
        'user_id',
        'account_number',
        'account_name',
        'investment_type',
        'initial_investment',
        'current_value',
        'total_returns',
        'return_percentage',
        'currency',
        'opening_date',
        'is_active',
        'holdings',
    ];

    protected $casts = [
        'initial_investment' => 'decimal:2',
        'current_value' => 'decimal:2',
        'total_returns' => 'decimal:2',
        'return_percentage' => 'decimal:4',
        'opening_date' => 'date',
        'is_active' => 'boolean',
        'holdings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
