<?php

use App\Http\Controllers\Dashboard\AccountController;
use App\Http\Controllers\Dashboard\AccountRequestController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\TransactionController;
use App\Http\Controllers\Dashboard\TransferController;
use App\Http\Controllers\Dashboard\PaymentController;
use App\Http\Controllers\Dashboard\CardController;
use App\Http\Controllers\Dashboard\BeneficiaryController;
use App\Http\Controllers\Dashboard\BillPaymentController;
use App\Http\Controllers\Dashboard\LoanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'redirect.if.admin'])->prefix('dashboard')->name('dashboard.')->group(function () {
    // Dashboard Home
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::post('/currency', [DashboardController::class, 'updateCurrency'])->name('currency.update');

    // Accounts
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/{account}', [AccountController::class, 'show'])->name('accounts.show');
    Route::post('/accounts/{account}/export', [AccountController::class, 'exportStatement'])->name('accounts.export');

    // Account Requests
    Route::get('/account-requests', [AccountRequestController::class, 'index'])->name('account-requests.index');
    Route::get('/account-requests/create', [AccountRequestController::class, 'create'])->name('account-requests.create');
    Route::post('/account-requests', [AccountRequestController::class, 'store'])->name('account-requests.store');
    Route::get('/account-requests/{accountRequest}', [AccountRequestController::class, 'show'])->name('account-requests.show');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Transfers
    Route::get('/transfers', [TransferController::class, 'index'])->name('transfers.index');
    Route::get('/transfers/create', [TransferController::class, 'create'])->name('transfers.create');
    Route::post('/transfers', [TransferController::class, 'store'])->name('transfers.store');

    // Cards
    Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
    Route::put('/cards/{card}/settings', [CardController::class, 'updateSettings'])->name('cards.update-settings');
    Route::post('/cards/{card}/freeze', [CardController::class, 'freeze'])->name('cards.freeze');
    Route::post('/cards/{card}/unfreeze', [CardController::class, 'unfreeze'])->name('cards.unfreeze');
    Route::post('/cards/{card}/report-lost', [CardController::class, 'reportLost'])->name('cards.report-lost');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

    // Beneficiaries
    Route::get('/beneficiaries', [BeneficiaryController::class, 'index'])->name('beneficiaries.index');
    Route::get('/beneficiaries/create', [BeneficiaryController::class, 'create'])->name('beneficiaries.create');
    Route::post('/beneficiaries', [BeneficiaryController::class, 'store'])->name('beneficiaries.store');
    Route::get('/beneficiaries/{beneficiary}/edit', [BeneficiaryController::class, 'edit'])->name('beneficiaries.edit');
    Route::put('/beneficiaries/{beneficiary}', [BeneficiaryController::class, 'update'])->name('beneficiaries.update');
    Route::delete('/beneficiaries/{beneficiary}', [BeneficiaryController::class, 'destroy'])->name('beneficiaries.destroy');

    // Bill Payments
    Route::get('/bill-payments', [BillPaymentController::class, 'index'])->name('bill-payments.index');
    Route::get('/bill-payments/create', [BillPaymentController::class, 'create'])->name('bill-payments.create');
    Route::post('/bill-payments', [BillPaymentController::class, 'store'])->name('bill-payments.store');
    Route::delete('/bill-payments/{billPayment}', [BillPaymentController::class, 'destroy'])->name('bill-payments.destroy');

    // Deposits
    Route::get('/deposits', function () {
        return inertia('dashboard/Deposits');
    })->name('deposits.index');

    // Loans
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
    Route::post('/loans/{loan}/toggle-autopay', [LoanController::class, 'toggleAutoPay'])->name('loans.toggle-autopay');

    // Investments
    Route::get('/investments', function () {
        return inertia('dashboard/Investments');
    })->name('investments.index');
});
