<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('login/google', [AuthController::class, 'loginWithGoogle']);
Route::group(
    ['middleware' => 'api.auth'],
    function ($router) {
        Route::get('login', [AuthController::class, 'loginWithGoogle']);
    }
);
