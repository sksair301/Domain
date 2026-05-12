<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\UserController;

Route::get('users', [UserController::class, 'index']);
