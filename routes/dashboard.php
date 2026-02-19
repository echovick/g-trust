<?php

use App\Http\Controllers\Dashboard\AccountController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\TransactionController;
use App\Http\Controllers\Dashboard\TransferController;
use App\Http\Controllers\Dashboard\PaymentController;
use App\Http\Controllers\Dashboard\CardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    // Dashboard Home
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::post('/currency', [DashboardController::class, 'updateCurrency'])->name('currency.update');

    // Accounts
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/{account}', [AccountController::class, 'show'])->name('accounts.show');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Transfers
    Route::get('/transfers', [TransferController::class, 'index'])->name('transfers.index');
    Route::get('/transfers/create', [TransferController::class, 'create'])->name('transfers.create');

    // Cards
    Route::get('/cards', [CardController::class, 'index'])->name('cards.index');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

    // Deposits
    Route::get('/deposits', function () {
        return inertia('dashboard/Deposits');
    })->name('deposits.index');

    // Loans
    Route::get('/loans', function () {
        return inertia('dashboard/Loans');
    })->name('loans.index');

    // Investments
    Route::get('/investments', function () {
        return inertia('dashboard/Investments');
    })->name('investments.index');
});
