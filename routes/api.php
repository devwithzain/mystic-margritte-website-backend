<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ContactFormController;
use App\Http\Controllers\Api\FormCheckoutController;
use App\Http\Controllers\Api\ResetPasswordController;

// Auth Routes
Route::post("/login", [AuthController::class, "login"]);
Route::post("/register", [AuthController::class, "register"]);
Route::post('/password/email', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // User Profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::get("/profile", [AuthController::class, "profile"]);
    Route::post('/profile/update/{id}', [AuthController::class, 'updateProfile']);
    Route::delete('/profile/delete', [AuthController::class, 'deleteAccount']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'deleteAll']);
});

// Admin Routes
Route::get("/getAllUsers", [AuthController::class, "getAllUsers"]);
Route::delete("/deleteUser/{id}", [AuthController::class, "deleteUser"]);

// Contact Forms
Route::post('/contact', [FormController::class, 'sendContactForm']);
Route::post('/contact-us', [ContactFormController::class, 'sendForm']);

// Products
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
});

// Blogs
Route::prefix('blogs')->group(function () {
    Route::get('/', [ServiceController::class, 'index']);
    Route::post('/', [ServiceController::class, 'store']);
    Route::get('/{id}', [ServiceController::class, 'show']);
    Route::put('/{id}', [ServiceController::class, 'update']);
    Route::delete('/{id}', [ServiceController::class, 'destroy']);
});

// Checkout
Route::post('/checkout', [CheckoutController::class, 'createSession']);
Route::post('/formCheckout', [FormCheckoutController::class, 'createSession']);