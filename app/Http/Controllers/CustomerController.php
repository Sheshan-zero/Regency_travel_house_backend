<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Loyalty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
// use Carbon\Carbon;

class CustomerController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'country_of_residence' => 'nullable|string',
            'nic' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'per_for_news'=> 'nullable|boolean',
        ]);

        $customer = Customer::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'country_of_residence' => $validated['country_of_residence'] ?? null,
            'nic' => $validated['nic'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'per_for_news'=>  $validated['per_for_news'] ?? null, 
        ]);

        $token = $customer->createToken('customer_token')->plainTextToken;

        return response()->json([
            'customer' => $customer,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $customer->createToken('customer_token')->plainTextToken;

        return response()->json([
            'customer' => $customer,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }


    public function index()
    {
        $customers = Customer::with('loyalty')
            ->withCount('bookings')
            ->withSum('loyaltyHistory as earned_points', 'points_earned')
            ->withSum('loyaltyHistory as redeemed_points', 'points_redeemed')
            ->get();

        return response()->json($customers);
    }

    public function show(Customer $customer, Loyalty $loyalty)
    {
        return response()->json([
            'id' => $customer->id,
            'full_name' => $customer->full_name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'country_of_residence' => $customer->country_of_residence,
            'date_of_birth' => $customer->date_of_birth,
            'bookings_count' => $customer->bookings()->count(),
            'loyalty' => [
                'earned_points' => $customer->loyalty->earned_points ?? 0,
                'redeemed_points' => $customer->loyalty->redeemed_points ?? 0,
            ]
        ]);
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        if (Auth::id() !== $customer->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:customers,email,' . $customer->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'country_of_residence' => 'nullable|string',
            'nic' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:Male,Female,Prefer not to say',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $customer->update($validated);
        return response()->json($customer);
    }


    // public function destroy(Customer $customer)
    // {
    //     $customer->delete();

    //     return response()->json(['message' => 'Customer deleted successfully']);
    // }

    public function loyaltySummary(): JsonResponse
    {
        $customer = Auth::user();

        $validEarned = $customer->loyaltyHistory()
            ->notExpired()
            ->sum('points_earned');

        $totalRedeemed = $customer->loyaltyHistory()
            ->sum('points_redeemed');

        $availablePoints = $validEarned - $totalRedeemed;


        $tier = 'Bronze';
        if ($validEarned >= 1000) {
            $tier = 'Gold';
        } elseif ($validEarned >= 500) {
            $tier = 'Silver';
        }

        return response()->json([
            'customer_id' => $customer->id,
            'available_points' => round($availablePoints, 2),
            'valid_earned' => round($validEarned, 2),
            'total_redeemed' => round($totalRedeemed, 2),
            'tier' => $tier,
            'history' => $customer->loyaltyHistory()
                ->orderBy('last_updated', 'desc')
                ->get()
                ->map(function ($record) {
                    return [
                        'earned' => $record->points_earned,
                        'redeemed' => $record->points_redeemed,
                        'date' => date('Y-m-d', strtotime($record->last_updated)),
                        'expired' => $record->isExpired(),
                        'membership_tier' => $record->tier,
                    ];
                })
        ]);
    }
}
