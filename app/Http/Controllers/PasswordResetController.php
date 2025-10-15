<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = strtolower($request->email);
        $otp = random_int(100000, 999999);

        // Store hashed token in password_reset_tokens (Laravel default table)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        // Try send email; if mail not configured, log the OTP for debugging
        try {
            if (config('mail.default')) {
                Mail::raw("Your 3Migs password reset code is: {$otp}", function ($message) use ($email) {
                    $message->to($email)->subject('3Migs Password Reset Code');
                });
            } else {
                Log::info('Password reset OTP (mail not configured)', ['email' => $email, 'otp' => $otp]);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to send password reset email; logging OTP', ['email' => $email, 'error' => $e->getMessage(), 'otp' => $otp]);
        }

        // For UX, store email in session for next steps
        session(['password_reset_email' => $email]);

        return redirect()->route('password.verify')->with('status', 'We sent a verification code to your email.');
    }

    public function showVerifyForm()
    {
        $email = session('password_reset_email');
        if (!$email) return redirect()->route('password.forgot');
        return view('auth.verify-otp', compact('email'));
    }

    /**
     * Quick action: send OTP for the currently authenticated user.
     */
    public function sendOtpForAuthenticatedUser(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $email = strtolower($user->email);
        $otp = random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        try {
            if (config('mail.default')) {
                Mail::raw("Your 3Migs password reset code is: {$otp}", function ($message) use ($email) {
                    $message->to($email)->subject('3Migs Password Reset Code');
                });
            } else {
                Log::info('Password reset OTP (mail not configured)', ['email' => $email, 'otp' => $otp]);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to send password reset email; logging OTP', ['email' => $email, 'error' => $e->getMessage(), 'otp' => $otp]);
        }

        session(['password_reset_email' => $email]);
        return redirect()->route('password.verify')->with('status', 'We sent a verification code to your email.');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', strtolower($request->email))->first();
        if (!$record) {
            return back()->withErrors(['otp' => 'Invalid or expired code.']);
        }

        // check created_at within 15 minutes and token match
        $validTime = now()->subMinutes(15);
        $matches = Hash::check($request->otp, $record->token);
        if (!$matches || $record->created_at < $validTime) {
            return back()->withErrors(['otp' => 'Invalid or expired code.']);
        }

        session(['password_reset_verified' => true, 'password_reset_email' => strtolower($request->email)]);
        return redirect()->route('password.reset');
    }

    public function showResetForm()
    {
        if (!session('password_reset_verified')) return redirect()->route('password.forgot');
        $email = session('password_reset_email');
        return view('auth.reset-password', compact('email'));
    }

    public function resetPassword(Request $request)
    {
        if (!session('password_reset_verified')) return redirect()->route('password.forgot');

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', strtolower($request->email))->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();

        // Clean up
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        session()->forget(['password_reset_verified', 'password_reset_email']);

        return redirect()->route('login')->with('success', 'Password updated. You can now log in.');
    }
}


