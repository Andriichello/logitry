<?php

use App\Http\Controllers\Auth as Auth;
use App\Http\Resources\User\MeResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'api.'], function () {
    Route::get('/example', function () {
        return ApiResponse::ok();
    });

    Route::post('/login', [Auth\LoginController::class, 'login']);
});

Route::group(['as' => 'api.', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/me', function () {
        return ApiResponse::ok(['me' => new MeResource(request()->user())]);
    });
});
