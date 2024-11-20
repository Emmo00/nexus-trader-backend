<?php

namespace Database\Seeders;

use App\Enums\AssetType;
use Illuminate\Database\Seeder;
use App\Models\Asset;

class AssetSeeder extends Seeder
{
    public function run()
    {
        $assets = [
            [
                'name' => 'EUR/USD',
                'symbol' => 'EURUSD',
                'category' => AssetType::CURRENCY_PAIR,
                'current_price' => 0,
                'from_currency' => 'EUR',
                'to_currency' => 'USD'
            ],
            [
                'name' => 'USD/JPY',
                'symbol' => 'USDJPY',
                'category' => AssetType::CURRENCY_PAIR,
                'current_price' => 0,
                'from_currency' => 'USD',
                'to_currency' => 'JPY'
            ],
            [
                'name' => 'GBP/USD',
                'symbol' => 'GBPUSD',
                'category' => AssetType::CURRENCY_PAIR,
                'current_price' => 0,
                'from_currency' => 'GBP',
                'to_currency' => 'USD'
            ],
            [
                'name' => 'USD/CHF',
                'symbol' => 'USDCHF',
                'category' => AssetType::CURRENCY_PAIR,
                'current_price' => 0,
                'from_currency' => 'USD',
                'to_currency' => 'CHF'
            ],
            [
                'name' => 'AUD/USD',
                'symbol' => 'AUDUSD',
                'category' => AssetType::CURRENCY_PAIR,
                'current_price' => 0,
                'from_currency' => 'AUD',
                'to_currency' => 'USD'
            ],
            [
                'name' => 'USD/CAD',
                'symbol' => 'USDCAD',
                'category' => AssetType::CURRENCY_PAIR,
                'current_price' => 0,
                'from_currency' => 'USD',
                'to_currency' => 'CAD'
            ],
            [
                'name' => 'NZD/USD',
                'symbol' => 'NZDUSD',
                'category' => AssetType::CURRENCY_PAIR,
                'current_price' => 0,
                'from_currency' => 'NZD',
                'to_currency' => 'USD'
            ],
            [
                'name' => 'EUR/JPY',
                'symbol' => 'EURJPY',
                'category' => AssetType::CURRENCY_PAIR,
                'current_price' => 0,
                'from_currency' => 'EUR',
                'to_currency' => 'JPY'
            ],
            [
                'name' => 'GBP/JPY',
                'symbol' => 'GBPJPY',
                'category' => AssetType::CURRENCY_PAIR,
                'current_price' => 0,
                'from_currency' => 'GBP',
                'to_currency' => 'JPY'
            ],
            [
                'name' => 'EUR/GBP',
                'symbol' => 'EURGBP',
                'category' => AssetType::CURRENCY_PAIR,
                'current_price' => 0,
                'from_currency' => 'EUR',
                'to_currency' => 'GBP'
            ],
            [
                'name' => 'EUR/CHF',
                'symbol' => 'EURCHF',
                'category' => AssetType::CURRENCY_PAIR,
                'current_price' => 0,
                'from_currency' => 'EUR',
                'to_currency' => 'CHF'
            ],
            [
                'name' => 'GBP/CHF',
                'symbol' => 'GBPCHF',
                'category' => AssetType::CURRENCY_PAIR,
                'current_price' => 0,
                'from_currency' => 'GBP',
                'to_currency' => 'CHF'
            ],
        ];


        foreach ($assets as $asset) {
            Asset::create($asset);
        }
    }
}
