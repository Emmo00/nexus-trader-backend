<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Ratchet\Client\WebSocket;
use Ratchet\Client\Connector;
use React\EventLoop\Factory;

class UpdateAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-assets';

    protected $description = 'Fetch assets from a WebSocket server and store them in the cache';

    public function handle()
    {
        $loop = Factory::create();
        $connector = new Connector($loop);

        $connector('wss://ws.derivws.com/websockets/v3?app_id=65980')
            ->then(function (WebSocket $conn) use ($loop) {
                $conn->send(json_encode(["active_symbols" => "full"]));

                $conn->on('message', function ($msg) use ($conn, $loop) {
                    $assets = json_decode($msg, true);

                    if (isset($assets['active_symbols'])) {
                        $all_assets = $assets['active_symbols'];

                        $assets = collect($all_assets)->filter(function ($asset) {
                            return in_array($asset['market'], ['forex', 'commodities', 'cryptocurrency']);
                        })->values()->all();

                        Log::info('fetched assets', [$assets]);
                    } else {
                        $assets = [];
                    }

                    // Cache the assets for 2 seconds
                    Cache::put('assets', $assets, now()->addSeconds(2000));

                    $conn->close();
                    $loop->stop();
                });
            }, function ($e) use ($loop) {
                $this->error("Connection failed: {$e->getMessage()}");
                $loop->stop();
            });

        $loop->run();
    }
}
