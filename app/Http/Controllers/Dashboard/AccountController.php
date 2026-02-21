<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as ResponseFacade;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function index(Request $request): Response
    {
        $accounts = $request->user()
            ->accounts()
            ->with(['cards', 'transactions' => function ($query) {
                $query->latest('transaction_date')->limit(5);
            }])
            ->get();

        return Inertia::render('dashboard/Accounts', [
            'accounts' => $accounts,
        ]);
    }

    public function show(Request $request, Account $account): Response
    {
        // Ensure the account belongs to the authenticated user
        if ($account->user_id !== $request->user()->id) {
            abort(403);
        }

        $account->load([
            'transactions' => function ($query) {
                $query->latest('transaction_date')->paginate(20);
            },
            'cards',
        ]);

        return Inertia::render('dashboard/AccountDetails', [
            'account' => $account,
        ]);
    }

    public function exportStatement(Request $request, Account $account)
    {
        // Ensure the account belongs to the authenticated user
        if ($account->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'format' => ['required', 'in:pdf,csv'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $transactions = $account->transactions()
            ->whereBetween('transaction_date', [$validated['start_date'], $validated['end_date']])
            ->orderBy('transaction_date', 'desc')
            ->get();

        if ($validated['format'] === 'csv') {
            return $this->exportCsv($account, $transactions, $validated['start_date'], $validated['end_date']);
        }

        return $this->exportPdf($account, $transactions, $validated['start_date'], $validated['end_date']);
    }

    private function exportCsv($account, $transactions, $startDate, $endDate)
    {
        $filename = 'statement-' . $account->account_number . '-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($account, $transactions, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            // Add header information
            fputcsv($file, ['Account Statement']);
            fputcsv($file, ['Account Number', $account->account_number]);
            fputcsv($file, ['Account Type', ucfirst($account->account_type)]);
            fputcsv($file, ['Currency', $account->currency]);
            fputcsv($file, ['Period', $startDate . ' to ' . $endDate]);
            fputcsv($file, ['Generated', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []); // Empty row

            // Add column headers
            fputcsv($file, [
                'Date',
                'Description',
                'Reference',
                'Type',
                'Category',
                'Debit',
                'Credit',
                'Balance',
                'Status',
            ]);

            // Add transactions
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_date,
                    $transaction->description,
                    $transaction->reference_number,
                    ucfirst($transaction->transaction_type),
                    ucfirst(str_replace('_', ' ', $transaction->category)),
                    $transaction->transaction_type === 'debit' ? number_format($transaction->amount, 2) : '',
                    $transaction->transaction_type === 'credit' ? number_format($transaction->amount, 2) : '',
                    number_format($transaction->balance_after, 2),
                    ucfirst($transaction->status),
                ]);
            }

            fclose($file);
        };

        return ResponseFacade::stream($callback, 200, $headers);
    }

    private function exportPdf($account, $transactions, $startDate, $endDate)
    {
        return view('statements.pdf', [
            'account' => $account,
            'transactions' => $transactions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'user' => $account->user,
        ]);
    }
}
