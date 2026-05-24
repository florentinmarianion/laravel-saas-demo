<?php

use Illuminate\Support\Facades\Route;
use Modules\CurrencyExchange\Http\Controllers\CurrencyExchangeController;

Route::middleware(['auth', 'permission:currency.view'])->group(function () {
    Route::get('/currency', [CurrencyExchangeController::class, 'index'])->name('currency.index');
    Route::get('/currency/rates', [CurrencyExchangeController::class, 'rates'])->name('currency.rates');
    Route::get('/currency/historical', [CurrencyExchangeController::class, 'historical'])->name('currency.historical');
});