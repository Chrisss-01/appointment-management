@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<style>
    .material-symbols-outlined.custom-tooth {
        font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 48;
        font-size: 48px;
        color: #1392EC;
        user-select: none;
    }

    .material-symbols-outlined.eye-icon {
        font-size: 20px;
        user-select: none;
    }

    .reveal-item {
        opacity: 0;
        transform: translateY(12px);
        transition: opacity 0.5s ease-out, transform 0.5s ease-out;
        will-change: opacity, transform;
    }

    .reveal-visible {
        opacity: 1;
        transform: translateY(0);
    }

    .input-transition {
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        opacity: 0.6;
        cursor: pointer;
    }
</style>

<div class="min-h-screen flex flex-col lg:flex-row overflow-hidden font-sans text-white bg-[#0F0F0F]">
    <!-- Left Panel: Registration Form -->
    <div class="w-full lg:w-[45%] min-h-screen flex flex-col justify-center px-6 py-12 md:px-12 lg:px-20 relative z-10 bg-[#0F0F0F] overflow-y-auto">
        <div class="max-w-md w-full mx-auto">

            <!-- Brand -->
            <div class="reveal-item flex items-center gap-3 mb-10">
                <div class="bg-[#1392EC] p-2 rounded-xl shadow-lg shadow-blue-500/20 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white w-6 h-6">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                        <path d="M8 11h8"/>
                        <path d="M12 7v8"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold tracking-tight text-white">Appoint</span>
            </div>

            <!-- Headings -->
            <div class="reveal-item mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-white mb-2">Create your account</h1>
                <p class="text-[#9CA3AF]">Register for the University of the Visayas clinic system.</p>
            </div>

            <!-- Form -->
            <form id="registerForm" action="{{ route('register.submit') }}" method="POST" class="space-y-5">
                @csrf
                
                @if($errors->any())
                    <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- University Email -->
                <div class="reveal-item space-y-1.5 relative">
                    <label for="email" class="block text-sm font-medium text-gray-300">University Email</label>
                    <div class="relative">
                        <input
                            type="email"
                            name="email"
                            id="email"
                            placeholder="yourname@uv.edu.ph"
                            required
                            class="input-transition w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC]"
                        >
                        <div id="email-error" class="hidden absolute left-0 -bottom-6 text-xs text-red-500 font-medium flex items-center gap-1">
                            <span>Only @uv.edu.ph emails are allowed.</span>
                        </div>
                    </div>
                </div>

                <!-- Name Row -->
                <div class="reveal-item grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="first_name" class="block text-sm font-medium text-gray-300">First Name</label>
                        <input
                            type="text"
                            name="first_name"
                            id="first_name"
                            placeholder="John"
                            required
                            class="input-transition w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC]"
                        >
                    </div>
                    <div class="space-y-1.5">
                        <label for="last_name" class="block text-sm font-medium text-gray-300">Last Name</label>
                        <input
                            type="text"
                            name="last_name"
                            id="last_name"
                            placeholder="Doe"
                            required
                            class="input-transition w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC]"
                        >
                    </div>
                </div>

                <!-- DOB and Sex Row -->
                <div class="reveal-item grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="dob" class="block text-sm font-medium text-gray-300">Date of Birth</label>
                        <div class="relative">
                            <input
                                type="date"
                                name="dob"
                                id="dob"
                                required
                                class="input-transition w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC]"
                            >
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label for="sex" class="block text-sm font-medium text-gray-300">Sex</label>
                        <select
                            name="sex"
                            id="sex"
                            required
                            class="input-transition w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC] appearance-none"
                        >
                            <option value="" disabled selected>Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <!-- Password -->
                <div class="reveal-item space-y-1.5">
                    <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="••••••••"
                            required
                            aria-describedby="password-length-error"
                            class="input-transition w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC] pr-12"
                        >
                        <button
                            type="button"
                            id="togglePassword"
                            class="hidden absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors focus:outline-none flex items-center justify-center"
                            aria-label="Toggle password visibility"
                        >
                            <span id="eye-icon-pass" class="material-symbols-outlined eye-icon">visibility_off</span>
                        </button>
                        <div id="password-length-error" class="hidden absolute left-0 -bottom-6 text-xs text-red-500 font-medium flex items-center gap-1">
                            <span>Password must be at least 8 characters long.</span>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="reveal-item space-y-1.5">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirm Password</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="••••••••"
                            required
                            aria-describedby="password-match-error"
                            class="input-transition w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC] pr-12"
                        >
                        <button
                            type="button"
                            id="toggleConfirmPassword"
                            class="hidden absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors focus:outline-none flex items-center justify-center"
                            aria-label="Toggle confirm password visibility"
                        >
                            <span id="eye-icon-confirm" class="material-symbols-outlined eye-icon">visibility_off</span>
                        </button>
                        <div id="password-match-error" class="hidden absolute left-0 -bottom-6 text-xs text-red-500 font-medium flex items-center gap-1">
                            <span>Passwords do not match.</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="reveal-item pt-2">
                    <button
                        type="submit"
                        id="registerBtn"
                        class="w-full py-3.5 bg-[#1392EC] text-white font-bold rounded-xl transition-all flex items-center justify-center gap-2 opacity-60 cursor-not-allowed"
                        disabled
                        aria-disabled="true"
                    >
                        Create Account
                    </button>
                </div>

                <!-- Footer Links -->
                <div class="reveal-item text-center mt-6">
                    <p class="text-sm text-[#9CA3AF]">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-[#1392EC] font-semibold hover:underline transition-all">Log In</a>
                    </p>
                    <p class="mt-4">
                        <a href="{{ url('/') }}" class="text-xs text-gray-500 hover:text-white/70 transition-colors duration-150 inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">arrow_back</span>
                            Back to Home
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Panel: Decorative -->
    <div class="hidden lg:flex flex-1 relative overflow-hidden border-l border-[#2d2d2d] bg-[#121212] items-center justify-center">
        <div class="absolute inset-0 z-0 opacity-20 pointer-events-none">
            <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="none" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg">
                <path d="M-100,500 C150,300 350,700 600,400 S850,100 1100,300" fill="none" stroke="#1392EC" stroke-width="2" stroke-opacity="0.5"></path>
                <path d="M-100,600 C200,400 400,800 650,500 S900,200 1150,400" fill="none" stroke="#1392EC" stroke-width="2" stroke-opacity="0.3"></path>
                <path d="M-100,700 C250,500 450,900 700,600 S950,300 1200,500" fill="none" stroke="#1392EC" stroke-width="1" stroke-opacity="0.5"></path>
                <circle cx="900" cy="200" r="30" fill="none" stroke="#1392EC" stroke-width="2" stroke-opacity="0.5"></circle>
                <circle cx="100" cy="800" r="60" fill="none" stroke="#1392EC" stroke-width="2" stroke-opacity="0.3"></circle>
            </svg>
        </div>

        <div class="relative z-10 flex flex-col items-center justify-center w-full p-12">
            <div class="grid grid-cols-2 gap-6 mb-16">
                <div class="w-40 h-40 rounded-2xl border border-[#1392EC]/20 bg-[#121212] flex items-center justify-center transition-transform hover:-translate-y-1 duration-300">
                    <span class="material-symbols-outlined text-[#1392EC] text-5xl opacity-80">stethoscope</span>
                </div>
                <div class="w-40 h-40 rounded-2xl border border-[#1392EC]/20 bg-[#121212] flex items-center justify-center transition-transform hover:-translate-y-1 duration-300">
                    <span class="material-symbols-outlined text-[#1392EC] text-5xl opacity-80">assignment</span>
                </div>
                <div class="w-40 h-40 rounded-2xl border border-[#1392EC]/20 bg-[#121212] flex items-center justify-center transition-transform hover:-translate-y-1 duration-300">
                    <span class="material-symbols-outlined text-[#1392EC] text-5xl opacity-80">dentistry</span>
                </div>
                <div class="w-40 h-40 rounded-2xl border border-[#1392EC]/20 bg-[#121212] flex items-center justify-center transition-transform hover:-translate-y-1 duration-300">
                    <span class="material-symbols-outlined text-[#1392EC] text-5xl opacity-80">pulse_alert</span>
                </div>
            </div>

            <div class="text-center max-w-sm">
                <h2 class="text-3xl font-bold text-[#1392EC] mb-4">University Health Services</h2>
                <p class="text-gray-400 leading-relaxed">
                    Providing quality medical attention and wellness support for the university community.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Progressive Reveal ---
        const revealItems = document.querySelectorAll('.reveal-item');
        revealItems.forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('reveal-visible');
            }, index * 100);
        });

        // --- 2. Password Toggle Logic ---
        function setupPasswordToggle(inputId, toggleBtnId, iconId) {
            const input = document.getElementById(inputId);
            const btn = document.getElementById(toggleBtnId);
            const icon = document.getElementById(iconId);

            if (input && btn && icon) {
                input.addEventListener('input', function() {
                    if (this.value.length > 0) {
                        btn.classList.remove('hidden');
                    } else {
                        btn.classList.add('hidden');
                        input.type = 'password';
                        icon.textContent = 'visibility_off';
                    }
                });

                btn.addEventListener('click', function() {
                    const isPassword = input.type === 'password';
                    if (isPassword) {
                        input.type = 'text';
                        icon.textContent = 'visibility';
                    } else {
                        input.type = 'password';
                        icon.textContent = 'visibility_off';
                    }
                });
            }
        }

        setupPasswordToggle('password', 'togglePassword', 'eye-icon-pass');
        setupPasswordToggle('password_confirmation', 'toggleConfirmPassword', 'eye-icon-confirm');

        // --- 3. Form Validation ---
        const registerForm = document.getElementById('registerForm');
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('email-error');
        const passwordInput = document.getElementById('password');
        const passwordLengthError = document.getElementById('password-length-error');
        const confirmInput = document.getElementById('password_confirmation');
        const confirmError = document.getElementById('password-match-error');
        const registerBtn = document.getElementById('registerBtn');

        function validateEmail() {
            const value = emailInput.value.trim();
            const isMatch = value.toLowerCase().endsWith('@uv.edu.ph');

            if (value.length > 0 && !isMatch) {
                emailInput.classList.remove('border-white/10', 'focus:ring-[#1392EC]', 'focus:border-[#1392EC]');
                emailInput.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                emailInput.setAttribute('aria-invalid', 'true');
                emailError.classList.remove('hidden');
                return false;
            } else {
                emailInput.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                emailInput.classList.add('border-white/10', 'focus:ring-[#1392EC]', 'focus:border-[#1392EC]');
                emailInput.removeAttribute('aria-invalid');
                emailError.classList.add('hidden');
                return isMatch;
            }
        }

        function validatePasswordLength() {
            const value = passwordInput.value;
            const isValid = value.length >= 8;

            if (value.length > 0 && !isValid) {
                passwordInput.classList.remove('border-white/10', 'focus:ring-[#1392EC]', 'focus:border-[#1392EC]');
                passwordInput.classList.add('border-red-500', 'ring-1', 'ring-red-500/40');
                passwordInput.setAttribute('aria-invalid', 'true');
                passwordLengthError.classList.remove('hidden');
            } else {
                passwordInput.classList.remove('border-red-500', 'ring-1', 'ring-red-500/40');
                passwordInput.classList.add('border-white/10', 'focus:ring-[#1392EC]', 'focus:border-[#1392EC]');
                passwordInput.removeAttribute('aria-invalid');
                passwordLengthError.classList.add('hidden');
            }

            return isValid;
        }

        function validatePasswords() {
            const pass = passwordInput.value;
            const confirm = confirmInput.value;

            if (pass && confirm) {
                if (pass !== confirm) {
                    confirmInput.classList.remove('border-white/10', 'focus:ring-[#1392EC]', 'focus:border-[#1392EC]');
                    confirmInput.classList.add('border-red-500', 'ring-1', 'ring-red-500/40');
                    confirmInput.setAttribute('aria-invalid', 'true');
                    confirmError.classList.remove('hidden');
                    return false;
                } else {
                    confirmInput.classList.remove('border-red-500', 'ring-1', 'ring-red-500/40');
                    confirmInput.classList.add('border-white/10', 'focus:ring-[#1392EC]', 'focus:border-[#1392EC]');
                    confirmInput.removeAttribute('aria-invalid');
                    confirmError.classList.add('hidden');
                    return true;
                }
            }

            confirmInput.classList.remove('border-red-500', 'ring-1', 'ring-red-500/40');
            confirmInput.classList.add('border-white/10', 'focus:ring-[#1392EC]', 'focus:border-[#1392EC]');
            confirmInput.removeAttribute('aria-invalid');
            confirmError.classList.add('hidden');

            return false;
        }

        function updateButtonState() {
            const isEmailValid = validateEmail();
            const isPasswordLengthValid = validatePasswordLength();
            const isPasswordMatch = validatePasswords();

            if (isEmailValid && isPasswordLengthValid && isPasswordMatch) {
                registerBtn.disabled = false;
                registerBtn.removeAttribute('aria-disabled');
                registerBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                registerBtn.classList.add('hover:bg-[#1392EC]/90', 'active:scale-[0.98]', 'shadow-lg', 'shadow-blue-500/20');
            } else {
                registerBtn.disabled = true;
                registerBtn.setAttribute('aria-disabled', 'true');
                registerBtn.classList.add('opacity-60', 'cursor-not-allowed');
                registerBtn.classList.remove('hover:bg-[#1392EC]/90', 'active:scale-[0.98]', 'shadow-lg', 'shadow-blue-500/20');
            }
        }

        if (emailInput && passwordInput && confirmInput) {
            emailInput.addEventListener('input', updateButtonState);
            emailInput.addEventListener('blur', updateButtonState);

            passwordInput.addEventListener('input', updateButtonState);
            passwordInput.addEventListener('blur', updateButtonState);

            confirmInput.addEventListener('input', updateButtonState);
            confirmInput.addEventListener('blur', updateButtonState);

            updateButtonState();
        }

        // --- 4. Submitting form logic ---
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                const isEmailValid = validateEmail();
                const isPasswordLengthValid = validatePasswordLength();
                const isPasswordMatch = validatePasswords();

                if (!isEmailValid || !isPasswordLengthValid || !isPasswordMatch) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endsection