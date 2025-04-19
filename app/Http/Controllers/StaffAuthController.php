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

    // public function register(Request $request): JsonResponse
    // {
    //     $admin = auth('staff')->user();

    //     if (!$admin || $admin->role !== 'Admin') {
    //         return response()->json(['message' => 'Unauthorized. Only admins can register staff.'], 403);
    //     }

    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:staff,email',
    //         'password' => 'required|string|min:6|confirmed',
    //         'role' => 'required|in:Admin,Manager,Support'
    //     ]);

    //     $staff = Staff::create([
    //         'name' => $validated['name'],
    //         'email' => $validated['email'],
    //         'password' => bcrypt($validated['password']),
    //         'role' => $validated['role']
    //     ]);

    //     // Send reset password email
    //     Password::broker('staff')->sendResetLink(['email' => $validated['email']]);

    //     return response()->json([
    //         'message' => 'Staff registered successfully',
    //         'staff' => $staff
    //     ], 201);
    // }



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

        // Send reset link and check response
        // $resetLinkStatus = Password::broker('staff')->sendResetLink(['email' => $validated['email']]);

        // if ($resetLinkStatus !== Password::RESET_LINK_SENT) {
        //     return response()->json([
        //         'message' => 'Staff registered, but failed to send reset email.',
        //         'staff' => $staff,
        //         'email_status' => $resetLinkStatus,
        //     ], 202); // Use 202 to show "accepted but partial success"
        // }

        return response()->json([
            'message' => 'Staff registered successfully',
            'staff' => $staff
        ], 201);
    }

    public function index()
    {
        $staff = Staff::all();
        return response()->json($staff);
    }


    public function update(Request $request, $id): JsonResponse
    {
        $admin = auth('staff')->user();
        if (!$admin || $admin->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $staff = Staff::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $id,
            'role' => 'required|in:Admin,Manager,Support'
        ]);

        $staff->update($validated);

        return response()->json(['message' => 'Staff updated successfully.', 'staff' => $staff]);
    }

    public function destroy($id): JsonResponse
    {
        $admin = auth('staff')->user();
        if (!$admin || $admin->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $staff = Staff::findOrFail($id);
        $staff->delete();

        return response()->json(['message' => 'Staff deleted successfully.']);
    }
}
