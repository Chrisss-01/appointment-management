<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        Log::info('[Password Reset Link] Requested for', ['email' => $request->email]);

        // We use the default passbroker. It handles generating tokens safely.
        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        // Whether the email was found or not, we return the same generic message
        // to prevent email enumeration attacks. We map the status to generic success if it fails with invalid user.
        if ($status === Password::INVALID_USER) {
            $status = Password::RESET_LINK_SENT;
            Log::info('[Password Reset Link] Requested for non-existent email, faking success', ['email' => $request->email]);
        }

        return back()->with('status', __($status));
    }

    /**
     * Display the password reset view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        if (!$token) {
            return redirect()->route('password.request')->withErrors(['email' => 'Invalid or missing password reset token.']);
        }

        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        // Attempt password reset
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
                
                Log::info('[Password Reset] Password reset successful', ['user_id' => $user->id, 'email' => $user->email]);
            }
        );

        // Map status messages to be more user-friendly if needed
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('auth_reason', 'Password has been reset successfully. Please log in.')
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
