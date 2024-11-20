<?php

namespace App\Http\Middleware;

use App\Models\Asset;
use App\Services\MarketPriceService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UpdateAssetPrices
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info("Starting price update");

        $marketPriceService = new MarketPriceService();
        $assets = Asset::where('last_updated', '>', now()->subSeconds(5));

        $assets->each(function ($asset) use ($marketPriceService) {
            $current_price = $marketPriceService->getAssetLatestPrice(
                $asset->from_currency,
                $asset->to_currency,
            );

            if (!is_null($current_price)) {
                $asset->current_price = $current_price;
                $asset->last_updated = now();

                $asset->save();
                Log::info('asset updated', ['asset' => $asset]);
            };
        });
        return $next($request);
    }
}
