<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TradeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
    Route::put('update', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
});


Route::get('assets', [AssetController::class, 'listAssets']);
Route::get('assets/featured', [AssetController::class, 'featuredAssets']);
Route::get('assets/{symbol}', [AssetController::class, 'getAssetPriceData']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('trades', [TradeController::class, 'placeTrade']);
    Route::get('trades', [TradeController::class, 'getAllTrades']);
    Route::get('trades/{id}', [TradeController::class, 'getTradeStatus']);
});

use App\Http\Controllers\PaymentController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('depositWithdraw', [PaymentController::class, 'depositWithdraw']);
    Route::post('withdraw', [PaymentController::class, 'withdraw']);
    Route::get('transactions', [PaymentController::class, 'transactionHistory']);
    Route::get('wallet/balance', [PaymentController::class, 'walletBalance']);
    Route::get('paystack/callback', [PaymentController::class, 'paystackCallback'])->name('paystack.callback');
});
