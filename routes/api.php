<?php

use App\Http\Controllers\Auth as Auth;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'api.'], function () {
    Route::get('/example', function () {
        return response()->json(['message' => 'OK']);
    });

    Route::post('/login', [Auth\LoginController::class, 'login']);
});

