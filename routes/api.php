<?php

use App\Http\Controllers\Api\ProjectControllerApiVue;
use App\Http\Controllers\Api\TaskControllerApi as ApiTaskControllerApi;
use App\Http\Controllers\Api\TaskControllerApiVue;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\DrawingController;
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



Route::middleware(['auth'])->group(function () {

//Tasks Apis
Route::prefix('tasks')->group(function(){
    Route::get('/', [TaskControllerApiVue::class, 'index']);
    Route::post('/', [TaskControllerApiVue::class, 'store']);
    Route::get('/{id}', [TaskControllerApiVue::class, 'show']);
    Route::patch('/{id}', [TaskControllerApiVue::class, 'update']);
    Route::delete('/{id}', [TaskControllerApiVue::class, 'destroy']);

    Route::patch('/{id}/status', [TaskControllerApiVue::class, 'updateStatus']);
    Route::post('/reorder', [TaskControllerApiVue::class, 'reorder']);
});

//Projects Apis
Route::prefix('projects')->group(function(){
    Route::get('/', [ProjectControllerApiVue::class, 'index']);
    Route::post('/', [ProjectControllerApiVue::class, 'store']);
    Route::get('/search', [ProjectControllerApiVue::class, 'search'])->name('search');
    Route::get('/{id}', [ProjectControllerApiVue::class, 'show']);
    Route::patch('/{id}', [ProjectControllerApiVue::class, 'update']);
    Route::delete('/{id}', [ProjectControllerApiVue::class, 'destroy']);

    Route::patch('/{id}/status', [ProjectControllerApiVue::class, 'updateStatus']);
    Route::post('/reorder', [ProjectControllerApiVue::class, 'reorder']);

});

Route::prefix('drawings')->group(function (){
    Route::get('/', [DrawingController::class, 'show']);
    Route::post('/', [DrawingController::class, 'upsert']);
});
});

