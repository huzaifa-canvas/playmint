<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ─── Registration ───────────────────────────────────────────────

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Registration successful.',
            'user' => $user,
            'has_pin_set' => !is_null($user->parent_pin),
            'token' => $token,
        ], 201);
    }

    // ─── Login ──────────────────────────────────────────────────────

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'status' => true,
            'user' => $user,
            'has_pin_set' => !is_null($user->parent_pin),
            'token' => $token,
        ]);
    }

    // ─── Logout ─────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['status' => true, 'message' => 'Logged out successfully']);
    }

    // ─── Get Authenticated User ─────────────────────────────────────

    public function user(Request $request)
    {
        return response()->json(['status' => true, 'user' => $request->user()]);
    }

    // ═══════════════════════════════════════════════════════════════
    // PARENT PIN
    // ═══════════════════════════════════════════════════════════════

    /**
     * Set or update the parent PIN (4–6 digits).
     * Requires authentication.
     */
    public function setPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|digits_between:4,6',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $user->parent_pin = Hash::make($request->pin);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Parent PIN set successfully.',
        ]);
    }

    /**
     * Verify the parent PIN.
     * Requires authentication.
     */
    public function verifyPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|digits_between:4,6',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        if (!$user->parent_pin) {
            return response()->json(['status' => false, 'message' => 'Parent PIN has not been set yet.'], 400);
        }

        if (!Hash::check($request->pin, $user->parent_pin)) {
            return response()->json(['status' => false, 'message' => 'Invalid PIN.'], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'PIN verified successfully.',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // FORGOT PASSWORD (OTP-based)
    // ═══════════════════════════════════════════════════════════════

    /**
     * Send OTP to user's email for password reset.
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Rate limit: prevent spamming OTPs (must wait 60 seconds)
        if ($user->otp_expires_at && Carbon::now()->diffInSeconds(Carbon::parse($user->otp_expires_at)) > 540) {
            return response()->json([
                'status' => false,
                'message' => 'Please wait 60 seconds before requesting another OTP.',
            ], 429);
        }

        $code = rand(100000, 999999);
        $user->otp = $code;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        // Mail::send('emails.otp', [
        //     'otp' => $code,
        //     'user' => $user,
        //     'purpose' => 'Password Reset',
        // ], function ($message) use ($user) {
        //     $message->to($user->email);
        //     $message->subject('Password Reset OTP - ' . config('app.name'));
        // });

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your email. (For testing: ' . $code . ')',
            //'otp_for_testing' => $code, // Remove in production
        ]);
    }

    /**
     * Verify OTP and reset password.
     */
    public function verifyAndResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (
            !$user ||
            $user->otp !== $request->otp ||
            Carbon::now()->gt(Carbon::parse($user->otp_expires_at))
        ) {
            return response()->json(['status' => false, 'message' => 'Invalid or expired OTP.'], 422);
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json(['status' => true, 'message' => 'Password reset successfully.']);
    }

    // ═══════════════════════════════════════════════════════════════
    // FORGOT PIN (OTP-based)
    // ═══════════════════════════════════════════════════════════════

    /**
     * Send OTP to user's email for PIN reset.
     * Requires authentication.
     */
    public function forgotPin(Request $request)
    {
        $user = $request->user();

        // Rate limit: prevent spamming OTPs (must wait 60 seconds)
        if ($user->otp_expires_at && Carbon::now()->diffInSeconds(Carbon::parse($user->otp_expires_at)) > 540) {
            return response()->json([
                'status' => false,
                'message' => 'Please wait 60 seconds before requesting another OTP.',
            ], 429);
        }

        $code = rand(100000, 999999);
        $user->otp = $code;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        // Mail::send('emails.otp', [
        //     'otp' => $code,
        //     'user' => $user,
        //     'purpose' => 'PIN Reset',
        // ], function ($message) use ($user) {
        //     $message->to($user->email);
        //     $message->subject('PIN Reset OTP - ' . config('app.name'));
        // });

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your email for PIN reset. (For testing: ' . $code . ')',
            //'otp_for_testing' => $code, // Remove in production
        ]);
    }

    /**
     * Verify OTP and reset Parent PIN.
     * Requires authentication.
     */
    public function verifyAndResetPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string',
            'pin' => 'required|digits_between:4,6',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        if (
            !$user->otp ||
            $user->otp !== $request->otp ||
            Carbon::now()->gt(Carbon::parse($user->otp_expires_at))
        ) {
            return response()->json(['status' => false, 'message' => 'Invalid or expired OTP.'], 422);
        }

        $user->parent_pin = Hash::make($request->pin);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json(['status' => true, 'message' => 'Parent PIN reset successfully.']);
    }

    // ═══════════════════════════════════════════════════════════════
    // AUTHENTICATED RESET / CHANGE (Password & PIN)
    // ═══════════════════════════════════════════════════════════════

    /**
     * Reset/Change password for authenticated user using current password.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['status' => false, 'message' => 'Current password does not match.'], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['status' => true, 'message' => 'Password updated successfully.']);
    }

    /**
     * Reset/Change Parent PIN for authenticated user using current PIN.
     */
    public function resetPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_pin' => 'required|digits_between:4,6',
            'pin' => 'required|digits_between:4,6',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        if (!$user->parent_pin) {
            return response()->json(['status' => false, 'message' => 'Parent PIN has not been set yet.'], 400);
        }

        if (!Hash::check($request->current_pin, $user->parent_pin)) {
            return response()->json(['status' => false, 'message' => 'Current PIN does not match.'], 422);
        }

        $user->parent_pin = Hash::make($request->pin);
        $user->save();

        return response()->json(['status' => true, 'message' => 'Parent PIN updated successfully.']);
    }
}
