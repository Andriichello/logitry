<?php

use App\Http\Controllers\Auth as Auth;
use App\Http\Resources\User\MeResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'api.'], function () {
    Route::post('/login', [Auth\LoginController::class, 'login']);

    Route::get('/example', fn() => ApiResponse::ok());
});

Route::group(['as' => 'api.', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/me', [Auth\MeController::class, 'me']);
});
