<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DomainController;

use App\Http\Controllers\API\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('domains', DomainController::class);

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::apiResource('branches', \App\Http\Controllers\Admin\BranchController::class);
        Route::apiResource('managers', \App\Http\Controllers\Admin\ManagerController::class);
        Route::apiResource('employees', \App\Http\Controllers\Admin\EmployeeController::class);
        Route::apiResource('domains', \App\Http\Controllers\Admin\DomainController::class);
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
