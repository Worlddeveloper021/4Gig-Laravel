<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\VerifyController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\RegistgerController;
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

    Route::get('/social/credentials/{provider}', function ($provider) {
        return config("services.$provider");
    });

    Route::post('register', RegistgerController::class)->name('register');
    Route::post('login', LoginController::class)->name('login');
    Route::post('verify', VerifyController::class)->middleware('auth:sanctum')->name('verify');

    Route::post('forgot-password/request', [ForgotPasswordController::class, 'request'])->name('forgot_password.request');
    Route::post('forgot-password/verify', [ForgotPasswordController::class, 'verify'])->name('forgot_password.verify');
    Route::post('forgot-password/reset', [ForgotPasswordController::class, 'reset'])->name('forgot_password.reset');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}/profiles', [CategoryController::class, 'profiles_index'])->name('categories.profiles.index');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::post('profile/upload/file', [ProfileController::class, 'upload_file'])->name('profile.upload_file');
        Route::match(['post', 'put'], 'profile', [ProfileController::class, 'store'])->name('profile.store');
        Route::put('profile/step-2', [ProfileController::class, 'store_step_2'])->name('profile.store.step_2');
    });
});
