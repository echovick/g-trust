<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // Redirect to bill payments as they serve the same purpose
        return redirect()->route('dashboard.bill-payments.index');
    }
}
