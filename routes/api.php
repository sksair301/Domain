<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DomainController;

use App\Http\Controllers\API\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::post('/domains/bulk-update', [\App\Http\Controllers\Admin\DomainController::class, 'bulkUpdate']);
    Route::apiResource('domains', \App\Http\Controllers\Admin\DomainController::class);
    
    // Admin Routes for managing data elements
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::apiResource('branches', \App\Http\Controllers\Admin\BranchController::class);
        Route::apiResource('users', \App\Http\Controllers\UserController::class);
        Route::apiResource('payments', \App\Http\Controllers\Admin\PaymentController::class);
    });

    // Manager Routes
    Route::middleware('manager')->prefix('manager')->group(function () {
        Route::get('/users', [\App\Http\Controllers\Manager\UserController::class, 'index']);
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
