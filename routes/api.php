<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Admin Routes
    Route::middleware('admin')->prefix('admin')->group(base_path('routes/admin.php'));

    // Manager Routes
    Route::middleware('manager')->prefix('manager')->group(base_path('routes/manager.php'));
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
