<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
Route::get('/whatsapp/webhook',[\App\Http\Controllers\WhatsappWebhookController::class,'verify'])->middleware('throttle:60,1');
Route::post('/whatsapp/webhook',[\App\Http\Controllers\WhatsappWebhookController::class,'receive'])->middleware('throttle:120,1');

// Rutas públicas (con limitador de tasa contra fuerza bruta)
Route::middleware('throttle:login-burst')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Rutas protegidas por Token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/lotes/{batch}/pesaje',[\App\Http\Controllers\Api\MeasurementController::class,'store'])->middleware(['throttle:60,1','audit-workspace']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
