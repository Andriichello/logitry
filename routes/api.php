<?php

use App\Http\Controllers\Auth as Auth;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'api.'], function () {
    Route::post('/login', [Auth\LoginController::class, 'login'])
        ->name('login');

    Route::get('/sign-ins', [Auth\SignInsController::class, 'signIns'])
        ->middleware('throttle:30,1')
        ->name('sign-ins');

    Route::get('/example', fn() => ApiResponse::ok())
        ->name('example');
});

Route::group(['as' => 'api.', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/me', [Auth\MeController::class, 'me'])
        ->name('me');
});
