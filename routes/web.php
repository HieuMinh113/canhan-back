<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\GithubController;


Route::get('/', function () {
    return view('welcome');
    
});
    Route::get('/cart/getsession', [CartController::class, 'getCart']);
    Route::post('/cart/addsession', [CartController::class, 'addToCart']);
    Route::put('/cart/updatesession', [CartController::class, 'updateCart']);
    Route::delete('/cart/deletesession', [CartController::class, 'removeFromCart']);
    Route::delete('/cart/session/clear', [CartController::class, 'clearCart']);

    Route::get('auth/google/redirect', [GoogleController::class, 'redirectToGoogle']);
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

    Route::get('auth/github/redirect', [GithubController::class, 'redirectToGithub']);
    Route::get('auth/github/callback', [GithubController::class, 'handleGithubCallback']);