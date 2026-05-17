<?php

namespace App\Support;

use App\Models\Transaction;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceiptPdf
{
    public static function download(Transaction $transaction, bool $isAdmin = false): StreamedResponse
    {
        $html = view('receipts.transaction', [
            'transaction' => $transaction,
            'isAdmin'     => $isAdmin,
        ])->render();

        $pdf = Browsershot::html($html)
            ->paperSize(100, 140, 'mm')
            ->showBackground()
            ->margins(0, 0, 0, 0)
            ->pdf();

        $filename = 'receipt-' . $transaction->reference_number . '.pdf';

        return response()->stream(function () use ($pdf) {
            echo $pdf;
        }, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length'      => strlen($pdf),
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
