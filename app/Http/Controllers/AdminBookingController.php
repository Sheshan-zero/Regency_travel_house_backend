<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Loyalty;
use App\Notifications\BookingConfirmed;


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

    public function update(Request $request, int $id): JsonResponse
    {
        $staff = Auth::guard('staff')->user();

        if ($staff->role !== 'Admin' && $staff->role !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking = Booking::with('customer')->find($id);
        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $request->validate([
            'status' => 'nullable|in:pending,confirmed,cancelled,completed',
            'payment_reference' => 'nullable|string|max:255'
        ]);

        // Store current status before update
        $oldStatus = $booking->status;

        // Update the booking
        $booking->update($request->only(['status', 'payment_reference']));

        //  Loyalty Points Logic
        if ($request->status === 'confirmed' && $oldStatus !== 'confirmed') {
            $customer = $booking->customer;
            $pointsEarned = $booking->total_price * 0.1;

            $booking->customer->notify(new BookingConfirmed($booking));
            // Add points to customer's total
            $customer->increment('loyalty_points', $pointsEarned);

            // Log the transaction in loyalty history
            Loyalty::create([
                'customer_id' => $customer->id,
                'points_earned' => $pointsEarned,
                'points_redeemed' => 0,
                'last_updated' => now()
            ]);
        }

        return response()->json([
            'message' => 'Booking updated',
            'booking' => $booking->fresh(['customer', 'package'])
        ]);
    }

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
