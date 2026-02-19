<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    protected $fillable = [
        'user_id',
        'loan_number',
        'loan_type',
        'loan_amount',
        'outstanding_balance',
        'interest_rate',
        'currency',
        'term_months',
        'remaining_months',
        'monthly_payment',
        'origination_date',
        'maturity_date',
        'next_payment_date',
        'status',
        'total_paid',
        'auto_pay',
        'linked_account_id',
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'origination_date' => 'date',
        'maturity_date' => 'date',
        'next_payment_date' => 'date',
        'auto_pay' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function linkedAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'linked_account_id');
    }
}
