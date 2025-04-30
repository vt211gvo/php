<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
,--------------------------------------------------------------------------
, API Routes
,--------------------------------------------------------------------------
,
, Here is where you can register API routes for your application. These
, routes are loaded by the RouteServiceProvider and all of them will
, be assigned to the "api" middleware group. Make something great!
,
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- Auth Routes ---
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // --- Bookings ---
    Route::get('/bookings', [BookingController::class, 'index'])->middleware('role:Admin,Manager,Client');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->middleware('role:Admin,Manager,Client');
    Route::post('/bookings', [BookingController::class, 'store'])->middleware('role:Admin,Manager');
    Route::put('/bookings/{booking}', [BookingController::class, 'update'])->middleware('role:Admin,Manager');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->middleware('role:Admin');

    // --- Services ---
    Route::get('/services', [ServiceController::class, 'index'])->middleware('role:Admin,Manager,Client');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->middleware('role:Admin,Manager,Client');
    Route::post('/services', [ServiceController::class, 'store'])->middleware('role:Admin,Manager');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->middleware('role:Admin,Manager');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->middleware('role:Admin');

    // --- Payments ---
    Route::get('/payments', [PaymentController::class, 'index'])->middleware('role:Admin,Manager,Client');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->middleware('role:Admin,Manager,Client');
    Route::post('/payments', [PaymentController::class, 'store'])->middleware('role:Admin,Manager');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->middleware('role:Admin,Manager');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->middleware('role:Admin');

    // --- Reviews ---
    Route::get('/reviews', [ReviewController::class, 'index'])->middleware('role:Admin,Manager,Client');
    Route::get('/reviews/{review}', [ReviewController::class, 'show'])->middleware('role:Admin,Manager,Client');
    Route::post('/reviews', [ReviewController::class, 'store'])->middleware('role:Admin,Manager');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->middleware('role:Admin,Manager');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->middleware('role:Admin');

    // --- Guests ---
    Route::get('/guests', [GuestController::class, 'index'])->middleware('role:Admin,Manager,Client');
    Route::get('/guests/{guest}', [GuestController::class, 'show'])->middleware('role:Admin,Manager,Client');
    Route::post('/guests', [GuestController::class, 'store'])->middleware('role:Admin,Manager');
    Route::put('/guests/{guest}', [GuestController::class, 'update'])->middleware('role:Admin,Manager');
    Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->middleware('role:Admin');
});
