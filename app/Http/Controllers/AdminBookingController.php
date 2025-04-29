<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Loyalty;
use Illuminate\Support\Facades\DB;
use App\Models\Package;
use App\Notifications\BookingConfirmed;
use Illuminate\Support\Facades\Log;


class AdminBookingController extends Controller
{
    public function index(): JsonResponse
    {
        $staff = Auth::guard('staff')->user();

        if ($staff->role !== 'Admin' && $staff->role !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $bookings = Booking::with(['customer', 'package'])->orderBy('created_at', 'desc')->get();

        return response()->json($bookings);
    }

    public function show(int $id): JsonResponse
    {
        $staff = Auth::guard('staff')->user();

        if ($staff->role !== 'Admin' && $staff->role !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking = Booking::with(['customer', 'package'])->find($id);

        return $booking
            ? response()->json($booking)
            : response()->json(['message' => 'Booking not found'], 404);
    }

    // public function update(Request $request, int $id): JsonResponse
    // {
    //     $staff = Auth::guard('staff')->user();

    //     if (!in_array($staff->role, ['Admin', 'manager'])) {
    //         return response()->json(['message' => 'Unauthorized'], 403);
    //     }

    //     $booking = Booking::with('customer')->find($id);
    //     if (!$booking) {
    //         return response()->json(['message' => 'Booking not found'], 404);
    //     }

    //     $request->validate([
    //         'travel_date' => 'nullable|date|after:today',
    //         'status' => 'nullable|in:pending,confirmed,completed,cancelled',
    //         'payment_reference' => 'nullable|string|max:255'
    //     ]);

    //     $oldStatus = $booking->status;
    //     $booking->update($request->only(['status', 'payment_reference', 'travel_date']));

    //     $newStatus = $request->status;
    //     $customer = $booking->customer;

    //     // Loyalty logic ONLY when transitioning to confirmed/completed from a non-qualified state
    //     if (in_array($newStatus, ['confirmed', 'completed']) && !in_array($oldStatus, ['confirmed', 'completed'])) {
    //         $pointsEarned = $booking->total_price * 0.1;

    //         $booking->customer->notify(new BookingConfirmed($booking));
    //         // Add points to customer's total
    //         $customer->increment('loyalty_points', $pointsEarned);

    //         Loyalty::create([
    //             'customer_id' => $customer->id,
    //             'points_earned' => $pointsEarned,
    //             'points_redeemed' => 0,
    //             'last_updated' => now()
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'Booking updated successfully.',
    //         'booking' => $booking->fresh(['customer', 'package'])
    //     ]);
    // }


    public function update(Request $request, int $id): JsonResponse
    {
        $staff = Auth::guard('staff')->user();

        if (!in_array($staff->role, ['Admin', 'manager'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking = Booking::with('customer')->find($id);
        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $validated = $request->validate([
            'travel_date' => 'nullable|date|after:today',
            'status' => 'nullable|in:pending,confirmed,completed,cancelled',
            'payment_reference' => 'nullable|string|max:255'
        ]);

        $oldStatus = $booking->status;

        // Update only provided fields
        if (isset($validated['travel_date'])) {
            $booking->travel_date = $validated['travel_date'];
        }
        if (isset($validated['status'])) {
            $booking->status = $validated['status'];
        }
        if (isset($validated['payment_reference'])) {
            $booking->payment_reference = $validated['payment_reference'];
        }

        $booking->save();

        $newStatus = $booking->status;
        $customer = $booking->customer;

        // Loyalty logic ONLY when transitioning to confirmed/completed from a non-qualified state
        if (in_array($newStatus, ['confirmed', 'completed']) && !in_array($oldStatus, ['confirmed', 'completed'])) {
            $pointsEarned = $booking->total_price * 0.1;

            $customer->notify(new BookingConfirmed($booking));
            $customer->increment('loyalty_points', $pointsEarned);

            Loyalty::create([
                'customer_id' => $customer->id,
                'points_earned' => $pointsEarned,
                'points_redeemed' => 0,
                'last_updated' => now()
            ]);
        }

        return response()->json([
            'message' => 'Booking updated successfully.',
            'booking' => $booking->fresh(['customer', 'package'])
        ]);
    }

    public function storeByStaff(Request $request): JsonResponse
    {
        $staff = Auth::guard('staff')->user();
        Log::info('Incoming staff booking', $request->all());
        
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'package_id' => 'required|exists:packages,id',
            'travel_date' => 'required|date|after:today',
            'number_of_travelers' => 'required|integer|min:1',
            'points_to_redeem' => 'nullable|numeric|min:0',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $package = Package::findOrFail($request->package_id);
        $basePrice = $package->price_per_person * $request->number_of_travelers;
        $redeem = floatval($request->points_to_redeem ?? 0);

        if ($redeem > 0) {
            $confirmedCount = $customer->bookings()
                ->whereIn('status', ['confirmed', 'completed'])
                ->count();

            if ($confirmedCount < 2) {
                return response()->json([
                    'message' => 'Redemption requires at least 2 confirmed or completed bookings.'
                ], 403);
            }

            $validEarned = $customer->loyaltyHistory()
                ->where('last_updated', '>', now()->subYears(2))
                ->sum('points_earned');

            $totalRedeemed = $customer->loyaltyHistory()->sum('points_redeemed');
            $availablePoints = $validEarned - $totalRedeemed;

            $redeem = min($redeem, $availablePoints, $basePrice);
        }

        $finalPrice = $basePrice - $redeem;

        DB::beginTransaction();

        try {
            $booking = Booking::create([
                'customer_id' => $customer->id,
                'package_id' => $package->id,
                'booking_date' => now(),
                'travel_date' => $request->travel_date,
                'number_of_travelers' => $request->number_of_travelers,
                'total_price' => $finalPrice,
                'status' => 'confirmed',
                'payment_reference' => null,
            ]);

            if ($redeem > 0) {
                $customer->decrement('loyalty_points', $redeem);

                Loyalty::create([
                    'customer_id' => $customer->id,
                    'points_earned' => 0,
                    'points_redeemed' => $redeem,
                    'last_updated' => now()
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Booking created by staff', 'booking' => $booking], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Booking failed', 'error' => $e->getMessage()], 500);
        }
    }



    //Admin dashboard
    public function summary(): JsonResponse
    {
        return response()->json([
            'total_packages' => \App\Models\Package::count(),
            'upcoming_bookings' => \App\Models\Booking::whereDate('travel_date', '>', now())->count(),
            'pending_quotes' => \App\Models\Quote::where('status', 'pending')->count(),
            'loyalty_customers' => \App\Models\Loyalty::where('points_earned', '>', 0)->count(),
            'registered_customers' => \App\Models\Customer::count(),
        ]);
    }

    // public function store(Request $request): JsonResponse
    // {
    //     $staff = Auth::guard('staff')->user();

    //     if (!in_array($staff->role, ['Admin', 'manager'])) {
    //         return response()->json(['message' => 'Unauthorized'], 403);
    //     }

    //     $request->validate([
    //         'package_id' => 'required|exists:packages,id',
    //         'travel_date' => 'required|date|after:today',
    //         'number_of_travelers' => 'required|integer|min:1',
    //         'points_to_redeem' => 'nullable|numeric|min:0'
    //     ]);

    //     $package = Package::findOrFail($request->package_id);
    //     $basePrice = $package->price_per_person * $request->number_of_travelers;
    //     $redeem = floatval($request->points_to_redeem ?? 0);

    //     // Step 1: Check redemption eligibility
    //     if ($redeem > 0) {
    //         $confirmedCount = $customer->bookings()
    //             ->where('status', 'confirmed')
    //             ->orWhere('status', 'completed')
    //             ->count();

    //         if ($confirmedCount < 2) {
    //             return response()->json([
    //                 'message' => 'Redemption requires at least 2 confirmed or completed bookings.'
    //             ], 403);
    //         }

    //         // Step 2: Fetch valid points
    //         $validEarned = $customer->loyaltyHistory()
    //             ->where('last_updated', '>', now()->subYears(2))
    //             ->sum('points_earned');

    //         $totalRedeemed = $customer->loyaltyHistory()->sum('points_redeemed');
    //         $availablePoints = $validEarned - $totalRedeemed;

    //         $redeem = min($redeem, $availablePoints, $basePrice);
    //     }

    //     $finalPrice = $basePrice - $redeem;

    //     DB::beginTransaction();

    //     try {
    //         $booking = Booking::create([
    //             'customer_id' => $customer->id,
    //             'package_id' => $package->id,
    //             'booking_date' => now(),
    //             'travel_date' => $request->travel_date,
    //             'number_of_travelers' => $request->number_of_travelers,
    //             'total_price' => $finalPrice,
    //             'status' => 'pending',
    //             'payment_reference' => null
    //         ]);

    //         if ($redeem > 0) {
    //             $customer->decrement('loyalty_points', $redeem);

    //             Loyalty::create([
    //                 'customer_id' => $customer->id,
    //                 'points_earned' => 0,
    //                 'points_redeemed' => $redeem,
    //                 'last_updated' => now()
    //             ]);
    //         }

    //         DB::commit();

    //         return response()->json($booking, 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['message' => 'Booking failed', 'error' => $e->getMessage()], 500);
    //     }
    // }

    public function verifyPaymentProof(Request $request, int $id): JsonResponse
    {
        $staff = Auth::guard('staff')->user();

        if (!in_array($staff->role, ['Admin', 'manager'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'payment_verified' => 'required|in:verified,rejected'
        ]);

        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $booking->update(['payment_verified' => $request->payment_verified]);

        return response()->json([
            'message' => 'Payment proof has been ' . $request->payment_verified,
            'booking' => $booking
        ]);
    }
}
