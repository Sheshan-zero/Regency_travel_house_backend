<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\StaffAuthController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\AdminQuoteController;
use App\Http\Controllers\LoyaltyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\Auth\StaffPasswordResetController;
use App\Http\Controllers\Auth\CustomerPasswordResetController;


// ---------------------
// Public Routes
// ---------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/packages', [PackageController::class, 'index']);
Route::get('/packages/search', [PackageController::class, 'smartSearch']);
Route::get('/packages/{id}', [PackageController::class, 'show']);
Route::get('/destinations', [DestinationController::class, 'index']);
Route::get('/destinations/{id}', [DestinationController::class, 'show']);
Route::post('/staff/login', [StaffAuthController::class, 'login']);
Route::post('/customer/register', [CustomerAuthController::class, 'register']);
Route::post('/customer/login', [CustomerAuthController::class, 'login']);
Route::get('/packages/{package_id}/itineraries', [ItineraryController::class, 'index']);
Route::post('/careers/apply', [JobApplicationController::class, 'store']);
Route::post('/staff/forgot-password', [StaffPasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/staff/reset-password', [StaffPasswordResetController::class, 'reset']);
Route::post('/customer/forgot-password', [CustomerPasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/customer/reset-password', [CustomerPasswordResetController::class, 'reset']);



// ---------------------
// Customer Routes
// ---------------------
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::put('/bookings/{id}/customer-update', [BookingController::class, 'updateByCustomer']);

    Route::get('/customer/profile', [CustomerAuthController::class, 'profile']);
    Route::post('/customer/logout', [CustomerAuthController::class, 'logout']);

    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);

    Route::get('/customer/loyalty', [CustomerAuthController::class, 'loyaltyPoints']);
    Route::get('/customer/loyalty/history', [CustomerAuthController::class, 'loyaltyHistory']);

    Route::get('/quotes', [QuoteController::class, 'index']);
    Route::post('/quotes', [QuoteController::class, 'store']);

    Route::get('/customer/notifications', [CustomerAuthController::class, 'notifications']);

});

// ---------------------
// Shared Staff Routes
// ---------------------
Route::middleware('auth:staff')->group(function () {
    Route::get('/staff/profile', [StaffAuthController::class, 'profile']);
    Route::post('/staff/logout', [StaffAuthController::class, 'logout']);

    Route::post('/packages', [PackageController::class, 'store']);
    Route::put('/packages/{id}', [PackageController::class, 'update']);
    Route::delete('/packages/{id}', [PackageController::class, 'destroy']);

    Route::post('/destinations', [DestinationController::class, 'store']);
    Route::put('/destinations/{id}', [DestinationController::class, 'update']);
    Route::delete('/destinations/{id}', [DestinationController::class, 'destroy']);

    Route::post('/itineraries', [ItineraryController::class, 'store']);
    Route::put('/itineraries/{id}', [ItineraryController::class, 'update']);
    Route::delete('/itineraries/{id}', [ItineraryController::class, 'destroy']);

    Route::post('/staff/register', [StaffAuthController::class, 'register']);

    Route::get('/dashboard/stats', [DashboardController::class, 'staffOverview']);
    Route::get('/dashboard/recent', [DashboardController::class, 'recentActivities']);
    Route::get('/staff/role', [DashboardController::class, 'getRole']);
});

// ---------------------
// Admin-only Routes
// ---------------------
Route::middleware(['auth:staff', 'staff.role:Admin'])->group(function () {
    Route::get('/admin/bookings', [AdminBookingController::class, 'index']);
    Route::get('/admin/bookings/{id}', [AdminBookingController::class, 'show']);

    Route::get('/admin/quotes', [AdminQuoteController::class, 'index']);
    Route::get('/admin/quotes/{id}', [AdminQuoteController::class, 'show']);
    Route::put('/admin/quotes/{id}/respond', [AdminQuoteController::class, 'respond']);

    Route::get('/images', [ImageController::class, 'index']);
    Route::post('/images', [ImageController::class, 'store']);
    Route::get('/images/{id}', [ImageController::class, 'show']);
    Route::delete('/images/{id}', [ImageController::class, 'destroy']);
});

// ---------------------
// Admin or Manager Routes
// ---------------------
Route::middleware(['auth:staff', 'staff.role:Admin,Manager'])->group(function () {
    Route::put('/admin/bookings/{id}', [AdminBookingController::class, 'update']);

    // Loyalty Point Management
    Route::get('/loyalty/customers', [LoyaltyController::class, 'index']);             // List all customers with loyalty
    Route::get('/loyalty/customers/{id}', [LoyaltyController::class, 'show']);         // View one customer's loyalty
    Route::put('/loyalty/customers/{id}/update', [LoyaltyController::class, 'update']); // Update points
    Route::post('/loyalty/customers/{id}/add', [LoyaltyController::class, 'addPoints']); // Add extra points

    // Customer Profiles
    Route::get('/admin/customers', [CustomerAuthController::class, 'all']);            // List all customers
    Route::get('/admin/customers/{id}', [CustomerAuthController::class, 'details']);   // View specific customer

    Route::get('/dashboard/stats', [StaffDashboardController::class, 'index']);

    Route::put('/admin/bookings/{id}/verify-payment', [AdminBookingController::class, 'verifyPaymentProof']);

});
