<?php

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\PackageController;
// use App\Http\Controllers\StaffAuthController;
// use App\Http\Controllers\DestinationController;
// use App\Http\Controllers\CustomerAuthController;
// use App\Http\Controllers\CustomerController;
// use App\Http\Controllers\AdminBookingController;
// use App\Http\Controllers\WishlistController;
// use App\Http\Controllers\ComplaintController;
// use App\Http\Controllers\BookingController;
// use App\Http\Controllers\ItineraryController;

// // Public routes
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
// Route::get('/packages', [PackageController::class, 'index']);
// Route::get('/packages/{id}', [PackageController::class, 'show']);
// Route::post('/staff/login', [StaffAuthController::class, 'login']);
// Route::post('/staff/login', [StaffAuthController::class, 'login']);
// Route::post('/customers/register', [CustomerController::class, 'register']);
// Route::post('/customers/login', [CustomerController::class, 'login']);
// Route::get('/destinations', [DestinationController::class, 'index']);
// Route::get('/destinations/{id}', [DestinationController::class, 'show']);
// Route::post('/customer/register', [CustomerAuthController::class, 'register']);
// Route::post('/customer/login', [CustomerAuthController::class, 'login']);
// Route::get('/packages/{package_id}/itineraries', [ItineraryController::class,'index']);

// // Protected Customer routes (require valid Sanctum token)            
// Route::middleware('auth:sanctum')->get('/booking/confirmed', [BookingController::class, 'confirmed']);


// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/profile', [AuthController::class, 'profile']);
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::get('/bookings', [BookingController::class, 'index']); // customer view
//     Route::get('/bookings/{id}', [BookingController::class, 'show']);


//     Route::get('/customer/loyalty-summary', [CustomerController::class, 'loyaltySummary']);
//     Route::post('/bookings', [BookingController::class, 'store']);
//     Route::get('/customer/profile', [CustomerAuthController::class, 'profile']);
//     Route::put('/customer/profile', [CustomerController::class, 'profile']);
//     Route::post('/customer/logout', [CustomerAuthController::class, 'logout']);

//     Route::get('/wishlist', [WishlistController::class, 'index']); //done
//     Route::post('/wishlist', [WishlistController::class, 'store']);
//     Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);

//     Route::get('/customer/loyalty', [CustomerAuthController::class, 'loyaltyPoints']);
//     Route::get('/customer/loyalty/history', [CustomerAuthController::class, 'loyaltyHistory']);
// });


// Route::middleware('auth:staff')->get('/admin/summary', [AdminBookingController::class, 'summary']);


// Protected Staff routes
// Route::middleware('auth:staff')->group(function () {
//     Route::post('/packages', [PackageController::class, 'store']);
//     Route::put('/packages/{id}', [PackageController::class, 'update']);
//     Route::delete('/packages/{id}', [PackageController::class, 'destroy']);

//     Route::post('/staff/register', [StaffAuthController::class, 'register']);
//     Route::get('/staff/profile', [StaffAuthController::class, 'profile']);
//     Route::post('/staff/logout', [StaffAuthController::class, 'logout']);

//     Route::get('/admin/bookings', [AdminBookingController::class, 'index']);
//     Route::get('/admin/bookings/{id}', [AdminBookingController::class, 'show']);
//     Route::put('/admin/bookings/{id}', [AdminBookingController::class, 'update']);

//     Route::get('/staff/profile', [StaffAuthController::class, 'profile']);
//     Route::post('/staff/logout', [StaffAuthController::class, 'logout']);

//     Route::post('/destinations', [DestinationController::class, 'store']);
//     Route::put('/destinations/{id}', [DestinationController::class, 'update']);
//     Route::delete('/destinations/{id}', [DestinationController::class, 'destroy']);

//     Route::post('/itineraries', [ItineraryController::class, 'store']);
//     Route::put('/itineraries/{id}', [ItineraryController::class, 'update']);
//     Route::delete('/itineraries/{id}', [ItineraryController::class, 'destroy']);

//     Route::get('/customer/all',[CustomerController::class,'index']);
// });

// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/customers/logout', [CustomerController::class, 'logout']);
//     Route::get('/customers', [CustomerController::class, 'index']);
//     Route::get('/customers/{customer}', [CustomerController::class, 'show']);
//     Route::put('/customers/{customer}', [CustomerController::class, 'update']);
//     Route::delete('/customers/{customer}', [CustomerController::class, 'destroy']);
// });


// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/bookings', [BookingController::class, 'index']); // Get all bookings
//     Route::post('/bookings', [BookingController::class, 'store']); // Create a new booking
//     Route::get('/bookings/{booking}', [BookingController::class, 'show']); // Get a specific booking
//     Route::put('/bookings/{booking}', [BookingController::class, 'update']); // Update a booking
//     Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']); // Delete a booking
// });

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/complaints', [ComplaintController::class, 'index']);
//     Route::post('/complaints', [ComplaintController::class, 'store']);
//     Route::get('/complaints/{complaint}', [ComplaintController::class, 'show']);
//     Route::put('/complaints/{complaint}', [ComplaintController::class, 'update']);
//     Route::delete('/complaints/{complaint}', [ComplaintController::class, 'destroy']);
// });



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\StaffAuthController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\AdminQuoteController;
use App\Http\Controllers\LoyaltyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\JobApplicationController;


/*Public Routes*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/staff/login', [StaffAuthController::class, 'login']);
// Route::post('/staff/register', [StaffAuthController::class, 'register']);

//Customer Registration by them
Route::post('/customers/register', [CustomerController::class, 'register']);
Route::post('/customers/login', [CustomerController::class, 'login']);

Route::get('/packages/categorized', [PackageController::class, 'categorizedPackages']);
Route::get('/packages/category/{category}', [PackageController::class, 'getByCategory']);


// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/packages', [PackageController::class, 'index']);
Route::get('/packages/{id}', [PackageController::class, 'show']);
Route::get('/destinations', [DestinationController::class, 'index']);
Route::get('/destinations/{id}', [DestinationController::class, 'show']);
Route::post('/staff/login', [StaffAuthController::class, 'login']);

//Customer Registration by Admins
Route::post('/customer/register', [CustomerAuthController::class, 'register']);
Route::post('/customer/login', [CustomerAuthController::class, 'login']);

Route::get('/packages', [PackageController::class, 'index']);
Route::get('/packages/{id}', [PackageController::class, 'show']);

Route::get('/destinations', [DestinationController::class, 'index']);
Route::get('/destinations/{id}', [DestinationController::class, 'show']);

Route::get('/packages/{package_id}/itineraries', [ItineraryController::class, 'index']);
Route::post('/careers/apply', [JobApplicationController::class, 'store']);
Route::get('/packages/search', [PackageController::class, 'smartSearch']);



/*Protected Routes - Customer (Sanctum)*/

Route::middleware('auth:sanctum')->get('/booking/confirmed', [BookingController::class, 'confirmed']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    // Customer Profile & Loyalty
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::put('/bookings/{id}/customer-update', [BookingController::class, 'updateByCustomer']);

    Route::get('/customer/profile', [CustomerAuthController::class, 'profile']);
    Route::put('/customer/profile', [CustomerController::class, 'profile']);
    Route::post('/customer/logout', [CustomerAuthController::class, 'logout']);
    Route::get('/customer/loyalty-summary', [CustomerController::class, 'loyaltySummary']);
    Route::get('/customer/loyalty', [CustomerAuthController::class, 'loyaltyPoints']);
    Route::get('/customer/loyalty/history', [CustomerAuthController::class, 'loyaltyHistory']);
    // Bookings
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::put('/bookings/{booking}', [BookingController::class, 'update']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);
    Route::get('/booking/confirmed', [BookingController::class, 'confirmed']);
    Route::get('/booking/transactions', [BookingController::class, 'transactions']);
    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);
    // Customers (for Admin view or Super Users)
    Route::post('/customers/logout', [CustomerController::class, 'logout']);
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{customer}', [CustomerController::class, 'show']);
    Route::put('/customers/{customer}', [CustomerController::class, 'update']);
    // Route::delete('/customers/{customer}', [CustomerController::class, 'destroy']);

    // Complaints
    Route::get('/complaints', [ComplaintController::class, 'index']);
    Route::post('/complaints', [ComplaintController::class, 'store']);
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show']);
    Route::put('/complaints/{complaint}', [ComplaintController::class, 'update']);
    Route::delete('/complaints/{complaint}', [ComplaintController::class, 'destroy']);

    Route::post('/quotes', [QuoteController::class, 'store']);
});

//  Protected Routes - Staff

Route::middleware('auth:staff')->group(function () {
    // Staff Auth
    Route::get('/staff/profile', [StaffAuthController::class, 'profile']);
    Route::post('/staff/logout', [StaffAuthController::class, 'logout']);

    // Admin Summary
    Route::get('/admin/summary', [AdminBookingController::class, 'summary']);
    Route::get('/admin/bookings', [AdminBookingController::class, 'index']);
    Route::get('/admin/bookings/{id}', [AdminBookingController::class, 'show']);
    Route::put('/admin/bookings/{id}', [AdminBookingController::class, 'update']);

    // Packages

    // Route::get('/wishlist', [WishlistController::class, 'index']);
    // Route::post('/wishlist', [WishlistController::class, 'store']);
    // Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);

    Route::get('/customer/loyalty', [CustomerAuthController::class, 'loyaltyPoints']);
    Route::get('/customer/loyalty/history', [CustomerAuthController::class, 'loyaltyHistory']);

    Route::get('/quotes', [QuoteController::class, 'index']);
    // Route::post('/quotes', [QuoteController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index']); //done
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);
});

// Shared Staff Routes
Route::middleware('auth:staff')->group(function () {
    Route::get('/staff/profile', [StaffAuthController::class, 'profile']);
    Route::post('/staff/logout', [StaffAuthController::class, 'logout']);
    Route::post('/staff/register', [StaffAuthController::class, 'register']);

    Route::post('/packages', [PackageController::class, 'store']);
    Route::put('/packages/{id}', [PackageController::class, 'update']);
    Route::delete('/packages/{id}', [PackageController::class, 'destroy']);

    // Destinations
    Route::post('/destinations', [DestinationController::class, 'store']);
    Route::put('/destinations/{id}', [DestinationController::class, 'update']);
    Route::delete('/destinations/{id}', [DestinationController::class, 'destroy']);

    // Itineraries
    Route::post('/itineraries', [ItineraryController::class, 'store']);
    Route::put('/itineraries/{id}', [ItineraryController::class, 'update']);
    Route::delete('/itineraries/{id}', [ItineraryController::class, 'destroy']);

    // Customers
    Route::get('/customer/all', [CustomerController::class, 'index']);
    Route::get('/customer/{customer}', [CustomerController::class, 'show']);
    Route::put('/customer/{customer}', [CustomerAuthController::class, 'update']);
    Route::delete('/customer/{customer}', [CustomerAuthController::class, 'destroy']);

    Route::post('/itineraries', [ItineraryController::class, 'store']);
    Route::put('/itineraries/{id}', [ItineraryController::class, 'update']);
    Route::delete('/itineraries/{id}', [ItineraryController::class, 'destroy']);


    Route::get('/dashboard/stats', [DashboardController::class, 'staffOverview']);
    Route::get('/dashboard/recent', [DashboardController::class, 'recentActivities']);
    Route::get('/staff/role', [DashboardController::class, 'getRole']);
});


Route::middleware(['auth:staff', 'staff.role:Admin'])->get('/staff/all', [StaffAuthController::class, 'index']);

Route::middleware(['auth:staff', 'staff.role:Admin'])->group(function () {
    Route::post('/staff/register', [StaffAuthController::class, 'register']);
    Route::get('/staff/all', [StaffAuthController::class, 'index']);
    Route::put('/staff/{id}', [StaffAuthController::class, 'update']);
    Route::delete('/staff/{id}', [StaffAuthController::class, 'destroy']);
});


// Admin-only Routes
Route::middleware(['auth:staff', 'staff.role:Admin'])->group(function () {
    Route::post('/staff/register', [StaffAuthController::class, 'register']);

    Route::get('/admin/bookings', [AdminBookingController::class, 'index']);
    Route::get('/admin/bookings/{id}', [AdminBookingController::class, 'show']);

    Route::get('/admin/quotes', [AdminQuoteController::class, 'index']);
    Route::get('/admin/quotes/{id}', [AdminQuoteController::class, 'show']);
    Route::put('/admin/quotes/{id}/respond', [AdminQuoteController::class, 'respond']);

    Route::get('/images', [ImageController::class, 'index']);
    Route::post('/images', [ImageController::class, 'store']);
    // Route::get('/images/{id}', [ImageController::class, 'show']);
    Route::delete('/images/{id}', [ImageController::class, 'destroy']);
    // routes/api.php
    Route::post('/images/multiple', [ImageController::class, 'storeMultiple']);
});


// Admin or Manager Routes

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
