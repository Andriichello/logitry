<?php

use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Web as Web;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::group(['as' => 'web.'], function () {
    Route::get('/login', [Web\Auth\LoginController::class, 'view'])
        ->middleware('guest')
        ->name('login.view');

    Route::post('/login', [Web\Auth\LoginController::class, 'login'])
        ->name('login');

    Route::get('/logout', [Web\Auth\LogoutController::class, 'view'])
        ->name('logout.view');

    Route::delete('/logout', [Web\Auth\LogoutController::class, 'logout'])
        ->name('logout');

    Route::get('/home', [Web\HomeController::class, 'view'])
        ->name('home.view');

    Route::get('/map', [Web\MapController::class, 'view'])
        ->name('map.view');

    Route::get('/map/data', [Web\MapController::class, 'get'])
        ->name('map.data');

    Route::get('/{abbreviation}', [Web\LandingController::class, 'view'])
        ->name('landing.view');

    Route::get('/{abbreviation}/data', [Web\LandingController::class, 'get'])
        ->name('landing.data');

    Route::get('/me', [MeController::class, 'me'])
        ->name('me.view');
});
