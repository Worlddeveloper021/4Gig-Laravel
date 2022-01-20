<?php

use App\Models\User;
use Orion\Facades\Orion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\VerifyController;
use App\Http\Controllers\Api\V1\RegistgerController;
use App\Http\Controllers\Api\V1\UsersProfileController;
use App\Http\Controllers\Api\V1\ForgotPasswordController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to the API Change !!!',
        'status' => 'successfully Message',
    ]);
});

Route::name('v1.')->prefix('v1')->group(function () {
    Route::get('/user/{user}/token', function (User $user) {
        return $user->createToken('test-token')->plainTextToken;
    });

    Route::post('register', RegistgerController::class)->name('register');
    Route::post('login', LoginController::class)->name('login');
    Route::post('verify', VerifyController::class)->middleware('auth:sanctum')->name('verify');

    Route::post('forgot-password/request', [ForgotPasswordController::class, 'request'])->name('forgot_password.request');
    Route::post('forgot-password/verify', [ForgotPasswordController::class, 'verify'])->name('forgot_password.verify');
    Route::post('forgot-password/reset', [ForgotPasswordController::class, 'reset'])->name('forgot_password.reset');

    Route::middleware('auth:sanctum')->group(function () {
        Orion::hasOneResource('users', 'profile', UsersProfileController::class)->withoutBatch();
    });
});
