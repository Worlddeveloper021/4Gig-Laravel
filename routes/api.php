<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\VerifyController;
use App\Http\Controllers\Api\V1\RegistgerController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::name('v1.')->prefix('v1')->group(function () {
    Route::post('register', RegistgerController::class)->name('register');
    Route::post('login', LoginController::class)->name('login');
    Route::post('verify', VerifyController::class)->name('verify');
});
