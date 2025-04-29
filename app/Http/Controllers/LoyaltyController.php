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

        // Create donation record
        $donation = Loyalty::create([
            'customer_id' => $customer->id,
            'points_earned' => 0,
            'points_redeemed' => 0,
            'donate' => $validated['donate'],
            'last_updated' => now(),
        ]);

        return response()->json([
            'message' => 'Donation successful.',
            'donation' => $donation
        ], 201);
    }
}
