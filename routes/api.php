<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\VerifyController;
use App\Http\Controllers\Api\V1\PackageController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\FirebaseController;
use App\Http\Controllers\Api\V1\RegistgerController;
use App\Http\Controllers\Api\V1\OnlineUserController;
use App\Http\Controllers\Api\V1\AccessTokenController;
use App\Http\Controllers\Api\V1\SocialAccountController;
use App\Http\Controllers\Api\V1\ForgotPasswordController;

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to the API Change !!!',
        'status' => 'successfully Message',
    ]);
});

Route::name('v1.')->prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return response()->json(UserResource::make($request->user()));
    })->name('user.current');

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

    Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::post('customers/verify', [CustomerController::class, 'verify'])->name('customers.verify');
    Route::post('customers/login', [CustomerController::class, 'login'])->name('customers.login');

    Route::get('reviews/{profile}', [ReviewController::class, 'show'])->name('reviews.show');

    Route::get('profile/{profile}', [ProfileController::class, 'show_by_id'])->name('profile.show_by_id');

    Route::get('profile/{profile}/packages', [PackageController::class, 'show'])->name('profile.package.show');

    Route::get('users/online', [OnlineUserController::class, 'index'])->name('users.online');

    Route::post('social-accounts/{provider}', [SocialAccountController::class, 'store'])->name('social-accounts.store');

    Route::post('firebase/push_notifications/{user}/send', [FirebaseController::class, 'send'])->name('firebase.push_notifications.send');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::post('profile/upload/file', [ProfileController::class, 'upload_file'])->name('profile.upload_file');
        Route::match(['post', 'put'], 'profile', [ProfileController::class, 'store'])->name('profile.store');
        Route::put('profile/step-2', [ProfileController::class, 'store_step_2'])->name('profile.store.step_2');

        Route::post('profile/packages', [PackageController::class, 'store'])->name('profile.package.store');

        Route::post('customers/card', [CustomerController::class, 'store_card'])->name('customers.card.store');
        Route::post('reviews/{profile}', [ReviewController::class, 'store'])->name('reviews.store');

        Route::get('call/access-token/voice', [AccessTokenController::class, 'voice'])->name('call.access-token.voice');
        Route::get('call/access-token/video', [AccessTokenController::class, 'video'])->name('call.access-token.video');
    });
});
