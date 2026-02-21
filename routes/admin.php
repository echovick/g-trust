<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Admin\CardController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\BillPaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Account Management
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/create', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::get('/accounts/{account}', [AccountController::class, 'show'])->name('accounts.show');
    Route::get('/accounts/{account}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
    Route::put('/accounts/{account}', [AccountController::class, 'update'])->name('accounts.update');
    Route::delete('/accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');
    Route::post('/accounts/{account}/toggle-status', [AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');
    Route::post('/accounts/{account}/fund', [AccountController::class, 'fundAccount'])->name('accounts.fund');
    Route::post('/accounts/transfer', [AccountController::class, 'intraAccountTransfer'])->name('accounts.transfer');

    // Account Bulk Operations
    Route::post('/accounts/bulk/toggle-status', [AccountController::class, 'bulkToggleStatus'])->name('accounts.bulk-toggle-status');
    Route::post('/accounts/bulk/destroy', [AccountController::class, 'bulkDestroy'])->name('accounts.bulk-destroy');
    Route::post('/accounts/bulk/fund', [AccountController::class, 'bulkFund'])->name('accounts.bulk-fund');
    Route::post('/accounts/bulk/export', [AccountController::class, 'bulkExport'])->name('accounts.bulk-export');

    // Transaction Management
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::post('/transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
    Route::post('/transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');
    Route::post('/transactions/{transaction}/reverse', [TransactionController::class, 'reverse'])->name('transactions.reverse');

    // Transaction Bulk Operations
    Route::post('/transactions/bulk/approve', [TransactionController::class, 'bulkApprove'])->name('transactions.bulk-approve');
    Route::post('/transactions/bulk/reject', [TransactionController::class, 'bulkReject'])->name('transactions.bulk-reject');
    Route::post('/transactions/bulk/destroy', [TransactionController::class, 'bulkDestroy'])->name('transactions.bulk-destroy');
    Route::post('/transactions/bulk/export', [TransactionController::class, 'bulkExport'])->name('transactions.bulk-export');

    // Transfer Management
    Route::get('/transfers', [TransferController::class, 'index'])->name('transfers.index');
    Route::get('/transfers/create', [TransferController::class, 'create'])->name('transfers.create');
    Route::post('/transfers', [TransferController::class, 'store'])->name('transfers.store');
    Route::get('/transfers/{transfer}', [TransferController::class, 'show'])->name('transfers.show');
    Route::post('/transfers/{transfer}/approve', [TransferController::class, 'approve'])->name('transfers.approve');
    Route::post('/transfers/{transfer}/cancel', [TransferController::class, 'cancel'])->name('transfers.cancel');

    // Card Management
    Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
    Route::get('/cards/{card}', [CardController::class, 'show'])->name('cards.show');
    Route::post('/cards/{card}/freeze', [CardController::class, 'freeze'])->name('cards.freeze');
    Route::post('/cards/{card}/unfreeze', [CardController::class, 'unfreeze'])->name('cards.unfreeze');
    Route::post('/cards/{card}/block', [CardController::class, 'block'])->name('cards.block');
    Route::post('/cards/{card}/report-lost', [CardController::class, 'reportLost'])->name('cards.report-lost');
    Route::put('/cards/{card}/settings', [CardController::class, 'updateSettings'])->name('cards.update-settings');
    Route::put('/cards/{card}/limits', [CardController::class, 'updateLimits'])->name('cards.update-limits');
    Route::post('/cards/{card}/toggle-feature', [CardController::class, 'toggleFeature'])->name('cards.toggle-feature');

    // Loan Management
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
    Route::post('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/adjust-payment', [LoanController::class, 'adjustPayment'])->name('loans.adjust-payment');
    Route::post('/loans/{loan}/write-off', [LoanController::class, 'writeOff'])->name('loans.write-off');
    Route::post('/loans/{loan}/toggle-auto-pay', [LoanController::class, 'toggleAutoPay'])->name('loans.toggle-auto-pay');

    // Bill Payment Management
    Route::get('/bill-payments', [BillPaymentController::class, 'index'])->name('bill-payments.index');
    Route::get('/bill-payments/{billPayment}', [BillPaymentController::class, 'show'])->name('bill-payments.show');
    Route::post('/bill-payments/{billPayment}/process', [BillPaymentController::class, 'process'])->name('bill-payments.process');
    Route::post('/bill-payments/{billPayment}/cancel', [BillPaymentController::class, 'cancel'])->name('bill-payments.cancel');
    Route::post('/bill-payments/{billPayment}/toggle-auto-pay', [BillPaymentController::class, 'toggleAutoPay'])->name('bill-payments.toggle-auto-pay');
});
