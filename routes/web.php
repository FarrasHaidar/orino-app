<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\GroomingController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PetHotelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Redirect::route('landing_page');
});

Route::get('/orino-page', [LandingController::class, 'landing_page'])->name('landing_page');

Auth::routes();

// Product

Route::get('/product', [ProductController::class, 'index_product'])->name('index_product');

Route::middleware(['admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin_page');

    Route::get('/product/create', [ProductController::class, 'create_product'])->name('create_product');

    Route::post('/product/create', [ProductController::class, 'store_product'])->name('store_product');

    Route::get('/product/{product}/edit', [ProductController::class, 'edit_product'])->name('edit_product');

    Route::patch('/product/{product}/update', [ProductController::class, 'update_product'])->name('update_product');

    Route::delete('/product/{product}', [ProductController::class, 'delete_product'])->name('delete_product');

    Route::post('/order/{order}/confirm', [OrderController::class, 'confirm_payment'])->name('confirm_payment');

    Route::get('/order_data', [OrderController::class, 'order_data'])->name('order_data');

    Route::get('/filter', [OrderController::class, 'filter'])->name('filter');

    Route::delete('/list-order/{order}', [OrderController::class, 'delete_order'])->name('delete_order');

    Route::get('/formGrooming-page', [GroomingController::class, 'show_groomingForm'])->name('show_groomingForm');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/grooming-page', [GroomingController::class, 'grooming_page'])->name('grooming_page');

    Route::get('/pethotel-page', [PetHotelController::class, 'pethotel_page'])->name('pethotel_page');

    Route::get('/product/{product}', [ProductController::class, 'show_product'])->name('show_product');

    // Cart

    Route::post('/cart/{product}', [CartController::class, 'add_to_cart'])->name('add_to_cart');

    Route::get('/cart', [CartController::class, 'show_cart'])->name('show_cart');

    Route::patch('/cart/{cart}', [CartController::class, 'update_cart'])->name('update_cart');

    Route::delete('/cart/{cart}', [CartController::class, 'delete_cart'])->name('delete_cart');

    // Checkout and Order

    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');

    Route::get('/order', [OrderController::class, 'index_order'])->name('index_order');

    Route::get('/order/{order}', [OrderController::class, 'show_order'])->name('show_order');

    Route::post('/order/{order}/pay', [OrderController::class, 'submit_payment_receipt'])->name('submit_payment_receipt');

    // Profile

    Route::get('/profile', [ProfileController::class, 'show_profile'])->name('show_profile');

    Route::post('/profile', [ProfileController::class, 'edit_profile'])->name('edit_profile');
});
