<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Loyalty;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(): JsonResponse
    {
        $customer = Auth::user();
        $bookings = Booking::with('package')->where('customer_id', $customer->id)->get();
        return response()->json($bookings);
    }

    public function show(int $id): JsonResponse
    {
        $booking = Booking::with('package')->find($id);
        if (!$booking || $booking->customer_id !== Auth::id()) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($booking);
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

        $package = \App\Models\Package::findOrFail($request->package_id);
        $basePrice = $package->price_per_person * $request->number_of_travelers;

        $redeem = floatval($request->points_to_redeem ?? 0);

        // Only check redemption logic if customer wants to redeem
        if ($redeem > 0) {
            // Step 1: Check if customer has at least 2 confirmed bookings
            $confirmedCount = $customer->bookings()
                ->where('status', 'confirmed')
                ->count();

            if ($confirmedCount < 2) {
                return response()->json([
                    'message' => 'You can only redeem points after completing at least 2 confirmed bookings.'
                ], 403);
            }

            // Step 2: Fetch non-expired loyalty records
            $validPoints = $customer->loyaltyHistory()
                ->where('points_earned', '>', 0)
                ->where('last_updated', '>', now()->subYears(2))
                ->sum('points_earned');

            $totalRedeemed = $customer->loyaltyHistory()->sum('points_redeemed');
            $availablePoints = $validPoints - $totalRedeemed;

            // Step 3: Cap redemption
            $redeem = min($redeem, $availablePoints);
            $redeem = min($redeem, $basePrice);
        }

        $finalPrice = $basePrice - $redeem;

        DB::beginTransaction();

        try {
            // Create the booking
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

            // Log redemption if points were used
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
            return response()->json([
                'message' => 'Booking failed',
                'error' => $e->getMessage()
            ], 500);
        }
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

        return response()->json(['message' => 'Booking updated. Pending admin confirmation.']);
    }

}
