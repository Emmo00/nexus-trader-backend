<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    /** @use HasFactory<\Database\Factories\TradeFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'asset_symbol',
        'asset_price',
        'amount',
        'multiplier',
        'prediction',
        'expiration_time',
        'status',
        'result',
        'payout'
    ];

    protected $casts = [
        'asset_price' => 'float',
        'amount' => 'float',
        'payout' => 'float',
    ];
}
