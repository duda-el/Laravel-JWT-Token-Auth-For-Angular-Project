<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CorsMiddleware; // Import your custom middlewar

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// Group routes for authentication
Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login'])->middleware(CorsMiddleware::class);
    Route::post('register', [AuthController::class, 'register'])->middleware(CorsMiddleware::class);
});

// Routes protected by auth:api middleware
Route::middleware(['auth:api', CorsMiddleware::class])->group(function () {
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});