<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpVerificationController extends Controller
{
    public function show(Request $request)
    {
        $email = $request->session()->get('otp_email');

        if (!$email) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp', ['email' => $email]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $email = $request->session()->get('otp_email');

        if (!$email) {
            return redirect()->route('register')
                ->withErrors(['email' => 'Session expired. Please register again.']);
        }

        $verification = EmailVerification::where('email', $email)->first();

        if (!$verification) {
            return redirect()->route('register')
                ->withErrors(['email' => 'No verification record found. Please register again.']);
        }

        // Check if max attempts exceeded
        if ($verification->attempts >= 5) {
            $verification->delete();
            $request->session()->forget('otp_email');

            return redirect()->route('register')
                ->withErrors(['email' => 'Too many failed attempts. Please register again.']);
        }

        // Check expiration
        if ($verification->isExpired()) {
            $verification->delete();
            $request->session()->forget('otp_email');

            return redirect()->route('register')
                ->withErrors(['email' => 'Verification code has expired. Please register again.']);
        }

        // Check OTP
        if (!Hash::check($request->otp, $verification->otp)) {
            $verification->increment('attempts');

            return back()->withErrors(['otp' => 'Invalid verification code. Please try again.']);
        }

        // OTP is valid — create the user
        $data = $verification->registration_data;

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'dob' => $data['dob'],
            'sex' => $data['sex'],
            'email_verified_at' => now(),
        ]);

        // Clean up
        $verification->delete();
        $request->session()->forget(['otp_email', 'otp_resent_at']);

        // Log in and redirect to onboarding
        Auth::login($user);

        return redirect()->route('onboarding.department');
    }

    public function resend(Request $request)
    {
        $email = $request->session()->get('otp_email');

        if (!$email) {
            return redirect()->route('register');
        }

        // Enforce 30-second cooldown
        $lastResent = $request->session()->get('otp_resent_at');
        if ($lastResent) {
            $elapsed = (int) now()->diffInSeconds($lastResent, absolute: true);
            if ($elapsed < 30) {
                $remaining = 30 - $elapsed;
                return back()->withErrors(['resend' => "Please wait {$remaining} seconds before resending."]);
            }
        }

        $verification = EmailVerification::where('email', $email)->first();

        if (!$verification) {
            return redirect()->route('register')
                ->withErrors(['email' => 'No verification record found. Please register again.']);
        }

        // Generate new OTP
        $otp = (string) random_int(100000, 999999);

        Log::info('[OTP Resend] OTP generated', ['email' => $email, 'otp_preview' => substr($otp, 0, 2) . '****']);

        $verification->update([
            'otp' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
        ]);

        // Send new OTP
        try {
            Log::info('[OTP Resend] Attempting to send OTP email', ['email' => $email, 'mailer' => config('mail.default')]);
            Mail::to($email)->send(new SendOtpMail($otp));
            Log::info('[OTP Resend] OTP email sent successfully', ['email' => $email]);
        } catch (\Throwable $e) {
            Log::error('[OTP Resend] Failed to send OTP email', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['resend' => 'Failed to send verification email. Please try again.']);
        }

        $request->session()->put('otp_resent_at', now()->toImmutable());

        return back()->with('success', 'A new verification code has been sent to your email.');
    }
}
