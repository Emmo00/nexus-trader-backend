<?php

namespace App\Enums;

enum AssetType
{
    const CURRENCY_PAIR = 'CURRENCY_PAIR';
    const STOCK = 'STOCK';

    const CRYPTOCURRENCY = 'CRYPTOCURRENCY';
    const COMMODITY = 'COMMODITY';

    public static function all()
    {
        return [
            static::CURRENCY_PAIR,
            static::STOCK,
            static::CRYPTOCURRENCY,
            static::COMMODITY,
        ];
    }
}
