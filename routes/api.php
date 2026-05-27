<?php

use App\Http\Controllers\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Route ini digunakan untuk endpoint yang tidak memerlukan CSRF.
| Khususnya webhook dari Midtrans Payment Gateway.
|
*/

// Webhook Midtrans - Dikecualikan dari CSRF di bootstrap/app.php
Route::post('/midtrans/notification', [MidtransWebhookController::class, 'handle'])
    ->name('midtrans.webhook');
