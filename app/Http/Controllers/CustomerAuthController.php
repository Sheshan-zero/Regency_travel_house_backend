<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    // public function register(Request $request): JsonResponse
    // {
    //     $request->validate([
    //         'full_name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:customers,email',
    //         'password' => 'required|string|min:6|confirmed'
    //     ]);

    //     $customer = Customer::create([
    //         'full_name' => $request->full_name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password)
    //     ]);

    //     $token = $customer->createToken('customer_token')->plainTextToken;

    //     return response()->json([
    //         'user' => $customer,
    //         'token' => $token
    //     ]);
    // }

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

    // public function update(Request $request, Customer $customer): JsonResponse
    // {
    //     if (Staff::id() !== $customer->id) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     $validated = $request->validate([
    //         'full_name' => 'sometimes|string|max:255',
    //         'email' => 'sometimes|string|email|max:255|unique:customers,email,' . $customer->id,
    //         'password' => 'nullable|string|min:8|confirmed',
    //         'phone' => 'nullable|string|max:15',
    //         'address' => 'nullable|string',
    //         'country_of_residence' => 'nullable|string',
    //         'nic' => 'nullable|string',
    //         'date_of_birth' => 'nullable|date',
    //         'gender' => 'nullable|string|in:Male,Female,Prefer not to say',
    //     ]);

    //     if (isset($validated['password'])) {
    //         $validated['password'] = Hash::make($validated['password']);
    //     }

    //     $customer->update($validated);
    //     return response()->json($customer);
    // }

    public function update(Request $request, Customer $customer): JsonResponse
{
    $staff = Auth::guard('staff')->user(); // get logged-in staff user

    if (!$staff || !in_array($staff->role, ['Admin', 'Manager'])) {
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
    } else {
        unset($validated['password']); // prevent updating with null password
    }

    $customer->update($validated);

    return response()->json($customer);
}


    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
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

}
