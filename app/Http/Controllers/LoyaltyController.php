<?php

namespace App\Http\Controllers;

use App\Models\Loyalty;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoyaltyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Loyalty $loyalty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loyalty $loyalty)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateByAdmin(Request $request, $id): JsonResponse
    {
        $staff = Auth::guard('staff')->user();

        if (!in_array($staff->role, ['Admin', 'manager'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'points_earned' => 'nullable|numeric|min:0',
            'points_redeemed' => 'nullable|numeric|min:0',
        ]);

        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        // Update customer total loyalty points
        $newTotalPoints = ($customer->loyalty_points ?? 0)
            + ($request->points_earned ?? 0)
            - ($request->points_redeemed ?? 0);

        if ($newTotalPoints < 0) $newTotalPoints = 0; // no negative points

        $customer->loyalty_points = $newTotalPoints;
        $customer->save();

        // Save new loyalty record
        $loyalty = Loyalty::create([
            'customer_id' => $customer->id,
            'points_earned' => $request->points_earned ?? 0,
            'points_redeemed' => $request->points_redeemed ?? 0,
            'last_updated' => now()
        ]);

        return response()->json([
            'message' => 'Loyalty points updated successfully.',
            'customer' => $customer,
            'loyalty' => $loyalty
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loyalty $loyalty) {}



    public function donatePoints(Request $request): JsonResponse
{
    $customer = Auth::guard('sanctum')->user();

    $validated = $request->validate([
        'donate' => 'required|numeric|min:1',
    ]);

    // Check if user has enough points to donate
    if (($customer->loyalty_points ?? 0) < $validated['donate']) {
        return response()->json([
            'message' => 'Not enough points to donate.',
        ], 400);
    }

    // Deduct the donated points from customer's loyalty points
    $customer->loyalty_points -= $validated['donate'];
    $customer->save();

    // Create donation record
    $donation = Loyalty::create([
        'customer_id' => $customer->id,
        'points_earned' => 0,
        'points_redeemed' => 0,
        'donate' => $validated['donate'],
        'last_updated' => now(),
    ]);

    // Calculate latest loyalty stats
    $loyaltyStats = [
        'valid_earned' => $customer->loyalty_points,
        'total_redeemed' => Loyalty::where('customer_id', $customer->id)->sum('points_redeemed'),
        'total_donated' => Loyalty::where('customer_id', $customer->id)->sum('donate'),
        'available_points' => $customer->loyalty_points,
        'membership_tier' => $this->calculateMembershipTier($customer->loyalty_points),
    ];

    return response()->json([
        'message' => 'Donation successful.',
        'donation' => $donation,
        'loyalty' => $loyaltyStats,
    ], 201);
}

/**
 * Determine membership tier based on loyalty points.
 */
private function calculateMembershipTier(int $points): string
{
    if ($points >= 5000) {
        return 'Gold';
    } elseif ($points >= 2000) {
        return 'Silver';
    } else {
        return 'Bronze';
    }
}

}
