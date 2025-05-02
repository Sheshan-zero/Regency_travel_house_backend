<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class StaffPasswordResetController extends Controller
{
    /**
     * Send reset link to staff email
     */
    // public function sendResetLinkEmail(Request $request): JsonResponse
    // {
    //     $request->validate([
    //         'email' => 'required|email|exists:staff,email',
    //     ]);

    //     $status = Password::broker('staff')->sendResetLink(
    //         $request->only('email')
    //     );

    //     return $status === Password::RESET_LINK_SENT
    //         ? response()->json(['message' => __($status)])
    //         : response()->json(['message' => __($status)], 400);
    // }

    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:staff,email',
        ]);

        $staff = \App\Models\Staff::where('email', $request->email)->first();

        // Deny if role is 'Admin'
        if ($staff->role === 'Admin') {
            return response()->json(['message' => 'Admin accounts are not allowed to reset password using this method.'], 403);
        }

        $status = Password::broker('staff')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 400);
    }


    /**
     * Reset the staff password using token
     */
    // public function reset(Request $request): JsonResponse
    // {
    //     $request->validate([
    //         'email' => 'required|email|exists:staff,email',
    //         'token' => 'required',
    //         'password' => 'required|string|min:6|confirmed',
    //     ]);

    //     $status = Password::broker('staff')->reset(
    //         $request->only('email', 'password', 'password_confirmation', 'token'),
    //         function ($staff, $password) {
    //             $staff->forceFill([
    //                 'password' => Hash::make($password),
    //             ])->save();
    //         }
    //     );

    //     return $status === Password::PASSWORD_RESET
    //         ? response()->json(['message' => 'Password reset successful.'])
    //         : response()->json(['message' => __($status)], 400);
    // }

    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:staff,email',
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $staff = \App\Models\Staff::where('email', $request->email)->first();

        // Deny if role is 'Admin'
        if ($staff->role === 'Admin') {
            return response()->json(['message' => 'Admin accounts cannot reset password via this method.'], 403);
        }

        $status = Password::broker('staff')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($staff, $password) {
                $staff->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successful.'])
            : response()->json(['message' => __($status)], 400);
    }
}
