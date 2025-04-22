<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')
    ->as('admin.')
    ->middleware(['auth', 'admin', 'verified'])
    ->group(function () {
        Route::resource('products', ProductController::class)->except('show');

    });

Route::prefix('products')
    ->as('front.products.')
    ->group(function () {
        Route::get('/', [ProductController::class, 'getProducts'])->name('index');
        Route::get('/{id}', [ProductController::class, 'getProductItem'])->name('show');

    });
