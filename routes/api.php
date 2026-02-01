<?php

use App\Http\Controllers\Api\TaskControllerApi as ApiTaskControllerApi;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\TaskControllerApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes (no authentication required)
Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'apiLogin']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [ApiAuthController::class, 'logout']);

    // Example of other protected routes
    Route::get('/tasks', [ApiTaskControllerApi::class, 'index']);
    Route::post('/tasks', [ApiTaskControllerApi::class, 'store']);
});