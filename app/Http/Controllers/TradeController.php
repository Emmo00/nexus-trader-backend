<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TradeController extends Controller
{
    /**
     * Place a new trade.
     */
    public function placeTrade(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_id' => 'required|exists:assets,id',
            'amount' => 'required|numeric|min:10', // Minimum trade amount
            'direction' => 'required|in:up,down', // Direction of the trade
            'expiration_time' => 'required|integer|min:5|max:86400', // Expiry time between 5 seconds to 24 hours
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $trade = Trade::create([
            'user_id' => Auth::id(),
            'asset_id' => $request->asset_id,
            'amount' => $request->amount,
            'direction' => $request->direction,
            'expiration_time' => $request->expiration_time,
            'status' => 'pending', // Default status
            'payout' => $this->calculatePayout($request->amount), // Calculate payout based on trade amount
        ]);

        return response()->json([
            'message' => 'Trade placed successfully',
            'data' => $trade,
        ], 201);
    }

    /**
     * View all trades of the authenticated user.
     */
    public function getAllTrades()
    {
        $trades = Trade::where('user_id', Auth::id())->get();

        return response()->json([
            'message' => 'Trades fetched successfully',
            'data' => $trades,
        ]);
    }

    /**
     * Fetch the status and result of a specific trade.
     */
    public function getTradeStatus($id)
    {
        $trade = Trade::where('user_id', Auth::id())->find($id);

        if (!$trade) {
            return response()->json([
                'message' => 'Trade not found or unauthorized access.',
            ], 404);
        }

        return response()->json([
            'message' => 'Trade status fetched successfully',
            'data' => $trade,
        ]);
    }

    /**
     * Calculate payout for a trade (a simple example, could be more complex based on trade logic).
     */
    private function calculatePayout($amount)
    {
        return $amount * 2; // Example: 2x payout (could depend on trade logic or asset type)
    }
}
