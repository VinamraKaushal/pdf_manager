<?php

use App\Http\Controllers\GuestCreditController;
use Illuminate\Support\Facades\Route;

Route::post('/guest-credit/init', [GuestCreditController::class, 'storeOrUpdate'])
    ->name('guest-credit.init');

Route::post('/guest-credit/deduct', [GuestCreditController::class, 'deductCredit'])
    ->name('guest-credit.deduct');
