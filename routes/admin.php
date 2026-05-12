<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\DomainController;

Route::apiResource('branches', BranchController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('payments', PaymentController::class);
Route::post('/domains/bulk-update', [DomainController::class, 'bulkUpdate']);
Route::apiResource('domains', DomainController::class);
