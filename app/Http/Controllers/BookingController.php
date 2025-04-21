<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Casts\Json;
use App\Models\Customer;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Loyalty;
use Illuminate\Support\Facades\DB;
use Laravel\Pail\ValueObjects\Origin\Console;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function index(): JsonResponse
    {
        $customer = Auth::user();
        $bookings = Booking::with('package')->where('customer_id', $customer->id)->get();
        return response()->json($bookings);
    }

    public function store(Request $request): JsonResponse
    {
        $customer = Auth::user();

        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'travel_date' => 'required|date|after:today',
            'number_of_travelers' => 'required|integer|min:1',
            'points_to_redeem' => 'nullable|numeric|min:0'
        ]);

        $package = Package::findOrFail($request->package_id);
        $basePrice = $package->price_per_person * $request->number_of_travelers;
        $redeem = floatval($request->points_to_redeem ?? 0);

        // Step 1: Check redemption eligibility
        if ($redeem > 0) {
            $confirmedCount = $customer->bookings()
                ->where('status', 'confirmed')
                ->orWhere('status', 'completed')
                ->count();

            if ($confirmedCount < 2) {
                return response()->json([
                    'message' => 'Redemption requires at least 2 confirmed or completed bookings.'
                ], 403);
            }

            // Step 2: Fetch valid points
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
                'status' => 'pending',
                'payment_reference' => null
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

            return response()->json($booking, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Booking failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function confirmed(): JsonResponse
    {
        $user = Auth::user();

        // You can adjust this based on whether it's Customer or Staff
        $bookings = $user->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->with('package') // if you want package info too
            ->get();

        return response()->json($bookings);
    }

    public function transactions(): JsonResponse
    {
        $user = Auth::user();

        // You can adjust this based on whether it's Customer or Staff
        $bookings = $user->bookings()
            ->whereIn('status', ['confirmed', 'pending'])
            ->with('package') // if you want package info too
            ->get();

        return response()->json($bookings);
    }
    
    public function updateByCustomer(Request $request, $id): JsonResponse
    {
        $booking = Booking::find($id);
        $customer = Auth::user();

        if (!$booking || $booking->customer_id !== $customer->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'number_of_people' => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:1000',
            'payment_reference' => 'nullable|file|mimes:jpg,jpeg,png,pdf'
        ]);

        // Save file if uploaded
        if ($request->hasFile('payment_reference')) {
            $path = $request->file('payment_reference')->store('payment_proofs', 'public');
            $booking->update([
                'payment_reference' => $path,
                'payment_verified' => 'pending'
            ]);
        }

        // Update editable fields
        $booking->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'number_of_people' => $request->number_of_people,
            'special_requests' => $request->special_requests,
        ]);

        // Optionally: notify admin via Notification/Event
        // Notification::send($adminUsers, new BookingUpdatedNotification($booking));

        return response()->json(['message' => 'Booking updated. Pending admin confirmation.']);
    }
}
