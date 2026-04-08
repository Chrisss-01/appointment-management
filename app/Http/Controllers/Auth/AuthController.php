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
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show student login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show staff login form.
     */
    public function showStaffLoginForm()
    {
        return view('auth.staff-login');
    }

    /**
     * Show student registration form.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle student login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role !== 'student') {
                Auth::logout();
                return back()->withErrors(['email' => 'This login is for students only.'])->onlyInput('email');
            }

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated.']);
            }

            /** @var \App\Models\User $user */
            return $this->redirectByRole($user);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle staff login.
     */
    public function staffLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!in_array($user->role, ['staff', 'admin'])) {
                Auth::logout();
                return back()->withErrors(['email' => 'This login is for staff only.']);
            }

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated.']);
            }

            /** @var \App\Models\User $user */
            return $this->redirectByRole($user);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle student registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'sex' => 'required|string|in:male,female,other',
            'email' => [
                'required',
                'email',
                'unique:users,email',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (!str_ends_with(strtolower($value), '@uv.edu.ph')) {
                        $fail('Only @uv.edu.ph email addresses are allowed.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Delete any existing verification for this email
        EmailVerification::where('email', $validated['email'])->delete();

        // Generate 6-digit OTP
        $otp = (string) random_int(100000, 999999);

        Log::info('[OTP Register] OTP generated', ['email' => $validated['email'], 'otp_preview' => substr($otp, 0, 2) . '****']);

        // Store registration data with hashed OTP
        EmailVerification::create([
            'email' => $validated['email'],
            'otp' => Hash::make($otp),
            'registration_data' => [
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'student',
                'dob' => $validated['dob'],
                'sex' => $validated['sex'],
            ],
            'expires_at' => now()->addMinutes(5),
        ]);

        // Send OTP email
        try {
            Log::info('[OTP Register] Attempting to send OTP email', ['email' => $validated['email'], 'mailer' => config('mail.default')]);
            Mail::to($validated['email'])->send(new SendOtpMail($otp));
            Log::info('[OTP Register] OTP email sent successfully', ['email' => $validated['email']]);
        } catch (\Throwable $e) {
            Log::error('[OTP Register] Failed to send OTP email', [
                'email' => $validated['email'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['email' => 'Failed to send verification email. Please try again.'])->withInput();
        }

        // Store email in session for OTP verification page
        $request->session()->put('otp_email', $validated['email']);

        return redirect()->route('otp.show');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    /**
     * Redirect user based on their role.
     */
    private function redirectByRole(User $user)
    {
        return match ($user->role) {
            'student' => redirect()->intended(route('student.dashboard')),
            'staff' => redirect()->route('staff.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('landing'),
        };
    }
}
