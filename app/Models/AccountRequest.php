<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountRequest extends Model
{
    protected $fillable = [
        'user_id',
        'account_type',
        'currency',
        'account_name',
        'purpose',
        'status',
        'approved_by',
        'rejection_reason',
        'approved_at',
        'rejected_at',
        'created_account_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'created_account_id');
    }
}
