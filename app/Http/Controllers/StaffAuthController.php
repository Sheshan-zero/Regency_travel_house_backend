<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;

class StaffAuthController extends Controller
{
    /**
     * Staff login with email and password
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $staff = Staff::where('email', $request->email)->first();

        if (! $staff || ! Hash::check($request->password, $staff->password) || !$staff->is_enabled) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        $token = $staff->createToken('staff_token')->plainTextToken;

        return response()->json([
            'user' => $staff,
            'token' => $token
        ]);
    }

    /**
     * Get the authenticated staff member’s profile
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * Logout and revoke all tokens for the staff
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $admin = auth('staff')->user();

        if (!$admin || $admin->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized. Only admins can register staff.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:Admin,Manager,Support'
        ]);

        $staff = Staff::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role']
        ]);

        // Send reset password email
        Password::broker('staff')->sendResetLink(['email' => $validated['email']]);

        return response()->json([
            'message' => 'Staff registered successfully',
            'staff' => $staff
        ], 201);
    }

}
