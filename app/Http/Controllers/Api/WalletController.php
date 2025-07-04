<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function show(Request $request)
    {
        $wallet = $request->user()->getOrCreateWallet();

        return response()->json([
            'success' => true,
            'data' => $wallet,
        ]);
    }

    public function transactions(Request $request)
    {
        $wallet = $request->user()->getOrCreateWallet();

        $query = WalletTransaction::where('wallet_id', $wallet->id);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    public function charge(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:100000',
            'payment_method' => 'required|in:bank_transfer,crypto,gift_card',
            'reference' => 'nullable|string',
        ]);

        $wallet = $request->user()->getOrCreateWallet();

        // In a real application, you would:
        // 1. Validate the payment with payment gateway
        // 2. Process the payment
        // 3. Add funds only after successful payment

        // For this demo, we'll just add the funds directly
        $transaction = $wallet->credit(
            $request->amount,
            'deposit',
            "Wallet charge via {$request->payment_method}",
            $request->reference,
            [
                'payment_method' => $request->payment_method,
                'reference' => $request->reference,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Wallet charged successfully',
            'data' => [
                'wallet' => $wallet->fresh(),
                'transaction' => $transaction,
            ],
        ]);
    }
}
