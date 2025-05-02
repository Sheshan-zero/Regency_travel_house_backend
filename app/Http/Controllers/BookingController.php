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
use Illuminate\Support\Str;
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
            ->with('package')
            ->get();

        return response()->json($bookings);
    }


    // public function updateByCustomer(Request $request, $id): JsonResponse
    // {        
    //     try {
    //         $booking = Booking::find($id);
    //         $customer = Auth::user();

    //         if (!$booking || $booking->customer_id !== $customer->id) {
    //             return response()->json(['message' => 'Unauthorized'], 403);
    //         }

    //         $validated = $request->validate([
    //             'start_date' => 'nullable|date',
    //             'travel_date' => 'nullable|date',
    //             'end_date' => 'nullable|date|after_or_equal:start_date',
    //             'number_of_people' => 'nullable|integer|min:1',
    //             'special_requests' => 'nullable|string|max:1000',
    //             'payment_reference' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
    //         ]);

    //         // Handle file upload
    //         if ($request->hasFile('payment_reference')) {
    //             $file = $request->file('payment_reference');
    //             $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
    //             $file->storeAs('public/payment_proofs', $filename);

    //             // Save filename to DB
    //             $booking->payment_reference = $filename;
    //             $booking->payment_verified = 'pending';
    //         }

    //         // Update booking fields
    //         $booking->start_date = $validated['start_date'] ?? $booking->start_date;
    //         $booking->travel_date = $validated['travel_date'] ?? $booking->travel_date;
    //         $booking->end_date = $validated['end_date'] ?? $booking->end_date;
    //         $booking->number_of_people = $validated['number_of_people'] ?? $booking->number_of_people;
    //         $booking->special_requests = $validated['special_requests'] ?? $booking->special_requests;
    //         // $booking->payment_reference = $validated['payment_reference'] ?? $booking->payment_reference;
    //         if ($request->hasFile('payment_reference')) {
    //             $file = $request->file('payment_reference');
    //             $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
    //             $file->storeAs('public/payment_proofs', $filename);

    //             $booking->payment_reference = $filename;
    //             $booking->payment_verified = 'pending';
    //         }




    //         $booking->save();

    //         return response()->json(['message' => 'Booking updated. Pending admin confirmation.'], 200);
    //     } catch (\Throwable $e) {
    //         return response()->json([
    //             'message' => 'Server error',
    //             'error' => $e->getMessage(),
    //             // Optional: remove trace in production for security
    //             'trace' => config('app.debug') ? $e->getTraceAsString() : 'Trace hidden',
    //         ], 500);
    //     }
    // }

    public function updateByCustomer(Request $request, $id): JsonResponse
    {
        try {
            $booking = Booking::find($id);
            $customer = Auth::user();

            if (!$booking || $booking->customer_id !== $customer->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Validate input fields
            $validated = $request->validate([
                // 'start_date' => 'nullable|date',
                'travel_date' => 'nullable|date',
                // 'end_date' => 'nullable|date|after_or_equal:start_date',
                'number_of_travelers' => 'nullable|integer|min:1',
                // 'special_requests' => 'nullable|string|max:1000',
                'payment_reference' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ]);

            // Handle receipt file upload if provided
            if ($request->hasFile('payment_reference')) {
                $file = $request->file('payment_reference');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/payment_proofs', $filename);

                $booking->payment_reference = $filename;
                $booking->payment_verified = 'pending';
            }

            // Update the rest of the fields (only if provided)
            // $booking->start_date = $validated['start_date'] ?? $booking->start_date;
            $booking->travel_date = $validated['travel_date'] ?? $booking->travel_date;
            // $booking->end_date = $validated['end_date'] ?? $booking->end_date;
            $booking->number_of_travelers = $validated['number_of_travelers'] ?? $booking->number_of_travelers;
                // $booking->special_requests = $validated['special_requests'] ?? $booking->special_requests;

            $booking->save();

            return response()->json(['message' => 'Booking updated. Pending admin confirmation.'], 200);
        } catch (\Throwable $e) {
            Log::error("Booking update failed", [
                'booking_id' => $id,
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : 'Trace hidden'
            ]);

            return response()->json([
                'message' => 'Server error during booking update.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
