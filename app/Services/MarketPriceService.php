<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MarketPriceService
{
    public function getAssetLatestPrice($fromAsset, $toAsset)
    {
        $response = Http::get("https://www.alphavantage.co/query?function=CURRENCY_EXCHANGE_RATE&from_currency={$fromAsset}&to_currency={$toAsset}&apikey=E0JEV0OO9FE6GPNY");

        $data = $response->json();

        Log::info("alphavantage response", [$data]);

        return $data['5. Exchange Rate'] ?? null;
    }
}
