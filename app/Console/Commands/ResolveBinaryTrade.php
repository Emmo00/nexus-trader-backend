<?php

namespace App\Console\Commands;

use App\Models\Trade;
use App\Models\User;
use App\Services\MarketPriceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResolveBinaryTrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:resolve-binary-trade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resolving pending trades...');

        // Fetch pending trades whose expiration time has passed
        $trades = Trade::where('status', 'pending')
            ->where('expiration_time', '<=', now())
            ->get();

        foreach ($trades as $trade) {
            // Fetch the current price of the asset (mocked here; replace with real service)
            $currentPrice = MarketPriceService::getAssetLatestPrice($trade->asset_symbol)['quote'] ?? null;

            if ($currentPrice === null) {
                Log::warning("Failed to fetch price for asset {$trade->asset_symbol}");
                continue;
            }

            // Determine result
            $result = $this->determineTradeResult($trade, $currentPrice);

            // Update trade details
            $trade->update([
                'status' => 'resolved',
                'result' => $result,
            ]);


            $user = User::find($trade->user_id);
            $user->credit($trade->payout);

            $this->info("Resolved trade ID {$trade->id}: Result - {$result}");
        }

        $this->info('Trade resolution process completed.');
    }

    /**
     * Determine the result of a trade.
     */
    private function determineTradeResult(Trade $trade, float $currentPrice): string
    {
        if ($trade->prediction === 'up') {
            return $currentPrice > $trade->asset_price ? 'won' : 'lost';
        } elseif ($trade->prediction === 'down') {
            return $currentPrice < $trade->asset_price ? 'won' : 'lost';
        }

        return 'lost'; // Default to 'lost' if something goes wrong
    }
}
