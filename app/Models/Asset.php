<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    /** @use HasFactory<\Database\Factories\AssetFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'symbol',
        'category',
        'current_price',
        'from_currency',
        'to_currency',
        'last_updated',
    ];

    protected $casts = [
        'current_price' => 'float',
    ];
}
