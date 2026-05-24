<?php

use Illuminate\Support\Facades\Route;
use Modules\CurrencyExchange\Http\Controllers\CurrencyExchangeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('currencyexchanges', CurrencyExchangeController::class)->names('currencyexchange');
});
