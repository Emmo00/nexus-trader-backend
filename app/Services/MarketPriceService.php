<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ratchet\Client\WebSocket;
use Ratchet\Client\Connector;
use React\EventLoop\Factory;

class MarketPriceService
{
    static public function getAssetLatestPrice($symbol)
    {
        $loop = Factory::create();
        $connector = new Connector($loop);
        $price_data = [];

        $connector('wss://ws.derivws.com/websockets/v3?app_id=65980')
            ->then(function (WebSocket $conn) use ($loop, $symbol, &$price_data) {
                $conn->send(json_encode(["ticks" => $symbol]));

                $conn->on('message', function ($msg) use ($conn, $loop, &$price_data) {
                    $response = json_decode($msg, true);

                    Log::info("returned asset data", [$response]);

                    if (isset($response['tick'])) {
                        $price_data = $response['tick'];
                    } else {
                        throw new \Exception("Invalid Asset Symbol");
                    }

                    $conn->close();
                    $loop->stop();
                });
            }, function ($e) use ($loop) {
                $loop->stop();
                throw new \Exception("Error getting price data");
            });

        $loop->run();

        return $price_data;
    }
}
