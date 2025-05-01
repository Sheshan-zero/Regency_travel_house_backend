<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class CustomerAuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $customer = Customer::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $customer->createToken('customer_token')->plainTextToken;

        return response()->json([
            'user' => $customer,
            'token' => $token
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (! $customer || ! Hash::check($request->password, $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $token = $customer->createToken('customer_token')->plainTextToken;

        return response()->json([
            'user' => $customer,
            'token' => $token
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function loyaltyPoints(Request $request): JsonResponse
    {
        return response()->json([
            'points' => $request->user()->loyalty_points
        ]);
    }

    public function loyaltyHistory(Request $request): JsonResponse
    {
        $history = $request->user()->loyaltyHistory()
            ->orderBy('last_updated', 'desc')->get();

        return response()->json($history);
    }

    public function all(): JsonResponse
    {
        $customers = \App\Models\Customer::orderBy('created_at', 'desc')->get();
        return response()->json($customers);
    }

    public function details($id): JsonResponse
    {
        $customer = \App\Models\Customer::find($id);

        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json($customer);
    }

    public function notifications(Request $request)
    {
        $user = $request->user();
        return response()->json($user->notifications()->latest()->get());
    }

}
