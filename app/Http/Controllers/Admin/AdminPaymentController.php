<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    /**
     * Display a listing of all payments for admin.
     */
    public function index(Request $request)
    {
        $query = Payment::query()->with('user'); // Eager load user

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('payment_type')) {
            $query->where('payment_type', $request->input('payment_type'));
        }
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->has('search')) { // Search by receipt, checkout ID, or user's phone/email
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('mpesa_receipt_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('checkout_request_id', 'like', '%' . $searchTerm . '%')
                    ->orWhere('phone_number', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('user', function ($uq) use ($searchTerm) {
                        $uq->where('email', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        $payments = $query->latest()->paginate(15);

        return response()->json($payments, 200);
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        return response()->json($payment->load('user'), 200);
    }
}