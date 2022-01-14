<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\VerifyController;
use App\Http\Controllers\Api\V1\RegistgerController;
use App\Http\Controllers\Api\V1\ForgotPasswordController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::name('v1.')->prefix('v1')->group(function () {
    Route::post('register', RegistgerController::class)->name('register');
    Route::post('login', LoginController::class)->name('login');
    Route::post('verify', VerifyController::class)->name('verify');

    Route::post('forgot-password/request', [ForgotPasswordController::class, 'request'])->name('forgot_password.request');
    Route::post('forgot-password/verify', [ForgotPasswordController::class, 'verify'])->name('forgot_password.verify');
    Route::post('forgot-password/reset', [ForgotPasswordController::class, 'reset'])->name('forgot_password.reset');
});
