<?php

namespace App\Http\Controllers;

use App\Console\Commands\UpdateAssets;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Services\MarketPriceService;
use Exception;

class AssetController extends Controller
{
    /**
     * List all assets grouped by category.
     */
    public function listAssets()
    {
        $assets = Cache::get('assets', (new UpdateAssets())->handle());

        return response()->json([
            'success' => true,
            'message' => 'Assets retrieved successfully',
            'data' => $assets,
        ]);
    }

    /**
     * List featured assets.
     */
    public function featuredAssets()
    {
        $featuredAssets = collect(Cache::get('assets', []))->random(8);

        return response()->json([
            'message' => 'Featured assets fetched successfully',
            'data' => $featuredAssets,
        ]);
    }

    /**
     * Get the price of a particular asset from its symbol
     * @param string $symbol
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getAssetPriceData(string $symbol)
    {
        $price_data = [];

        try {
            $price_data = MarketPriceService::getAssetLatestPrice($symbol);

            Cache::put("{$symbol}-price-data", $price_data, 2); // cache for 2 seconds
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => "$symbol price data",
            'data' => $price_data
        ]);
    }
}
