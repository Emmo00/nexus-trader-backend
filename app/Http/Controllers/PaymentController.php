<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Helpers\PaystackHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function walletBalance()
    {
        return response()->json([
            'message' => 'wallet balance',
            'data' => request()->user()->wallet,
        ]);
    }
    /**
     * Deposit or Withdraw funds.
     *
     * Handles both deposit and withdrawal based on the request type.
     */
    public function depositWithdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:deposit,withdraw', // 'deposit' or 'withdraw'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $amount = $request->amount;
        $type = $request->type;

        if ($type === 'deposit') {
            $reference = 'deposit_' . uniqid();
            $response = PaystackHelper::initiatePayment($user->email, $amount, $reference);

            if ($response && $response->status) {
                // Store transaction record (pending status)
                $transaction = new Transaction([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => 'deposit',
                    'status' => 'pending',
                    'reference' => $reference,
                ]);
                $transaction->save();

                return response()->json([
                    'message' => 'Deposit initialized successfully',
                    'data' => $response->data
                ], 200);
            }

            return response()->json(['message' => 'Error initializing deposit'], 500);
        }

        if ($type === 'withdraw') {
            // Ensure the user has enough balance to withdraw
            if ($user->balance < $amount) {
                return response()->json(['message' => 'Insufficient balance'], 400);
            }

            // Process withdrawal logic here (e.g., using Paystack transfer or other methods)

            // Record withdrawal transaction (pending)
            $transaction = new Transaction([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'withdraw',
                'status' => 'pending',
                'reference' => 'withdraw_' . uniqid(),
            ]);
            $transaction->save();

            return response()->json(['message' => 'Withdrawal request created. Processing...'], 200);
        }
    }

    /**
     * Handle Paystack callback after successful deposit.
     */
    public function paystackCallback(Request $request)
    {
        $reference = $request->get('reference');
        $response = PaystackHelper::verifyPayment($reference);

        if ($response && $response->status) {
            $transaction = Transaction::where('reference', $reference)->first();

            if ($transaction && $transaction->status === 'pending') {
                // Update the transaction status and user's balance
                $transaction->status = 'completed';
                $transaction->save();

                // Update user balance
                $user = $transaction->user;
                $user->balance += $transaction->amount;
                $user->save();

                return response()->json(['message' => 'Deposit successful'], 200);
            }
        }

        return response()->json(['message' => 'Deposit verification failed'], 400);
    }

    /**
     * Fetch transaction history (deposit, withdrawal, trades) for the user.
     */
    public function transactionHistory()
    {
        $user = Auth::user();

        // Fetch all deposit, withdrawal, and trade transactions for the user
        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Transaction history fetched successfully',
            'data' => $transactions,
        ]);
    }
}
