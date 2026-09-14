<?php

use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\Webhook\SePayWebhookController;
use Illuminate\Support\Facades\Route;

// Webhook server-to-server từ SePay (xác thực qua Authorization Bearer, không CSRF)
Route::post('/webhooks/sepay', [SePayWebhookController::class, 'invoke'])->name('webhooks.sepay');

// API Gợi ý "Có thể bạn cũng thích" dựa trên thuật toán Apriori
Route::get('/recommendations', [RecommendationController::class, 'index'])->name('api.recommendations');
