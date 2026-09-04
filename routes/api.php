<?php

use App\Http\Controllers\Webhook\SePayWebhookController;
use Illuminate\Support\Facades\Route;

// Webhook server-to-server từ SePay (xác thực qua Authorization Bearer, không CSRF)
Route::post('/webhooks/sepay', [SePayWebhookController::class, 'invoke'])->name('webhooks.sepay');
