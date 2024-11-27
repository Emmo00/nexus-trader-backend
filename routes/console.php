<?php

use App\Console\Commands\ResolveBinaryTrade;
use App\Console\Commands\UpdateAssets;
use Illuminate\Support\Facades\Schedule;

Schedule::command(UpdateAssets::class)->everyTenSeconds();
Schedule::command(ResolveBinaryTrade::class)->everySecond();
