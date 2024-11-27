<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\User;
use App\Models\Wallet;
use App\Services\MarketPriceService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            'symbol' => 'required',
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

        /**
         * @var User
         */
        $user = $request->user();
        /** @var Wallet */
        $wallet = $user->wallet;

        // check wallet balance
        if ($wallet->balance < $request->amount) {
            return response()->json([
                'message' => 'Insufficient Balance',
            ], 400);
        }

        // get asset current price
        $symbol = $request->symbol;

        try {
            $price_data = Cache::get("{$symbol}-price-data", function () use ($symbol) {
                return MarketPriceService::getAssetLatestPrice($symbol);
            });
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "invalid asset symbol",
            ], 400);
        }

        $trade = Trade::create([
            'user_id' => Auth::id(),
            'asset_symbol' => $request->symbol,
            'asset_price' => $price_data['quote'],
            'amount' => $request->amount,
            'prediction' => $request->direction,
            'expiration_time' => Carbon::now()->addSeconds((int)$request->expiration_time),
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
            'data' => [
                'trade' => $trade,
                'wallet' => request()->user()->wallet,
            ]
        ]);
    }

    /**
     * Calculate payout for a trade (a simple example, could be more complex based on trade logic).
     */
    private function calculatePayout($amount, $multiplier = 1)
    {
        return $amount * 0.8 * $multiplier; // Example: .8x payout (could depend on trade logic or asset type)
    }
}
