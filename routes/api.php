<?php

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BookNowController;
use App\Http\Controllers\Api\NewsletterController;
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

    // Blog
    Route::post('/blogs/{blog}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // Orders
    Route::get('/user/orders', [OrderController::class, 'getAllOrdersForUser']);
});

// Orders
Route::post('/placedOrder', [OrderController::class, 'placeOrder']);
Route::get('/admin/orders', [OrderController::class, 'getAllOrders']);

// Admin Routes
Route::get("/getAllUsers", [AuthController::class, "getAllUsers"]);
Route::delete("/deleteUser/{id}", [AuthController::class, "deleteUser"]);

// Contact Forms
Route::post('/contact', [FormController::class, 'sendContactForm']);
Route::post('/book-now', [BookNowController::class, 'sendBookForm']);

// Products
Route::get('/products', [ProductController::class, 'index']);
Route::post('/product', [ProductController::class, 'store']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::post('/product/{id}', [ProductController::class, 'update']);
Route::delete('/product/{id}', [ProductController::class, 'destroy']);

// Blogs
Route::get('/blogs', [BlogController::class, 'index']);
Route::post('/blog', [BlogController::class, 'store']);
Route::get('/blog/{id}', [BlogController::class, 'show']);
Route::post('/blog/{id}', [BlogController::class, 'update']);
Route::delete('/blog/{id}', [BlogController::class, 'destroy']);

// Newsletter
Route::get('/newsletter/subscribes', [NewsletterController::class, 'index']);
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
Route::get('/newsletter/verify/{token}', [NewsletterController::class, 'verify'])->name('newsletter.verify');

// Payment
Route::post('/payment-intent', function (Request $request) {
    Stripe::setApiKey(env('STRIPE_SECRET'));

    $paymentIntent = PaymentIntent::create([
        'amount' => $request->amount,
        'currency' => $request->currency,
        'automatic_payment_methods' => [
            'enabled' => true,
        ],
    ]);

    return response()->json([
        'clientSecret' => $paymentIntent->client_secret,
    ]);
});