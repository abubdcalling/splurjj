<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{

    // public function sendResetOTP(Request $request)
    // {
    //     $request->validate(['email' => 'required|email']);

    //     $user = User::where('email', $request->email)->first();

    //     if (!$user) {
    //         return response()->json(['success' => false, 'message' => 'User not found.'], 404);
    //     }

    //     $otp = rand(100000, 999999);

    //     $user->reset_otp = $otp;
    //     $user->otp_expires_at = Carbon::now()->addMinutes(10);
    //     $user->save();

    //     Mail::raw("Your password reset OTP is: $otp", function ($message) use ($user) {
    //         $message->to($user->email)
    //             ->subject('Password Reset OTP');
    //     });

    //     return response()->json(['success' => true, 'message' => 'OTP sent to your email.']);
    // }

    public function sendResetOTP(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $otp = rand(100000, 999999);

        // Store OTP in cache or a temp table, since there's no user to attach it to
        Cache::put('reset_otp_' . $request->email, [
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10)
        ], now()->addMinutes(10));

        Mail::raw("Your password reset OTP is: $otp", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Password Reset OTP');
        });

        return response()->json(['success' => true, 'message' => 'OTP sent to your email.']);
    }

    // public function verifyResetOTP(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'otp' => 'required|digits:6',
    //     ]);

    //     $user = User::where('email', $request->email)
    //         ->where('reset_otp', $request->otp)
    //         ->first();

    //     if (!$user || Carbon::now()->gt($user->otp_expires_at)) {
    //         return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 400);
    //     }

    //     $user->otp_verified_at = Carbon::now();
    //     $user->save();

    //     return response()->json(['success' => true, 'message' => 'OTP verified. You may now reset your password.']);
    // }



    public function verifyResetOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)
            ->where('reset_otp', $request->otp)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP or user not found.'
            ], 400);
        }

        if (!$user->otp_expires_at || Carbon::now()->gt($user->otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired.'
            ], 400);
        }

        // OTP is valid
        $user->otp_verified_at = Carbon::now();
        $user->reset_otp = null; // clear OTP
        $user->otp_expires_at = null; // clear expiration
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'OTP verified. You may now reset your password.'
        ]);
    }


    public function passwordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)
            ->whereNotNull('otp_verified_at')
            ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired reset link or verification.'], 403);
        }

        $user->password = bcrypt($request->password);
        $user->reset_otp = null;
        $user->otp_expires_at = null;
        $user->otp_verified_at = null;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password reset successful.']);
    }
    // Register user
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 400);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => $user
            ], 201);
        } catch (Exception $e) {
            Log::error('Error registering user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to register user.'
            ], 500);
        }
    }

    // Login user and get token
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 400);
            }

            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Invalid credentials.'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token
            ]);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Login failed.'
            ], 500);
        }
    }

    // Get authenticated user
    public function me()
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'User details fetched successfully.',
                'data' => auth()->user()
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user details.'
            ], 500);
        }
    }

    // Logout user (invalidate token)
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout.'
            ], 500);
        }
    }

    // Send password reset link to email
    public function sendResetEmailLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['success' => true, 'message' => __($status)])
            : response()->json(['success' => false, 'message' => __($status)], 400);
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'], // Laravel expects a `new_password_confirmation` field for confirmation
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }
}
