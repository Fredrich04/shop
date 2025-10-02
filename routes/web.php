<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\AdminController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/paypal/payment', [PayPalController::class, 'createPayment'])->name('paypal.payment');
Route::get('/paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [ShopController::class, 'index'])->name('shop.index');
    Route::prefix('api')->group(function () {
        Route::post('/cart/add', [ShopController::class, 'addToCart'])->name('api.cart.add');
        Route::get('/cart', [ShopController::class, 'getCart'])->name('api.cart.get');
        Route::put('/cart/update', [ShopController::class, 'updateCartItem'])->name('api.cart.update');
        Route::delete('/cart/remove', [ShopController::class, 'removeFromCart'])->name('api.cart.remove');
        Route::post('/checkout', [ShopController::class, 'checkout'])->name('api.checkout');
        Route::get('/orders', [ShopController::class, 'orders'])->name('api.orders');
         Route::get('/products', [ShopController::class, 'getProducts'])->name('api.products');
    });
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('/auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'isAdmin'])->group(function () {
    Route::resource('products', ProductController::class);

    Route::resource('categories', ShopController::class);

    Route::patch('/users/{user}/update-role', [UserController::class, 'updateRole'])->name('users.updateRole');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.delete');
});

require __DIR__.'/auth.php';
