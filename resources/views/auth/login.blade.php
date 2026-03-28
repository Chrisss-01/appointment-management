@extends('layouts.auth')

@section('title', 'Log In')

@section('content')
<style>
    /* Custom Tooth Icon Styling */
    .material-symbols-outlined.custom-tooth {
        font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 48;
        font-size: 48px;
        color: #1392EC;
        user-select: none;
    }

    /* Eye Icon Styling */
    .material-symbols-outlined.eye-icon {
        font-size: 20px;
        user-select: none;
    }

    /* Progressive Reveal Animation */
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
    
    /* Input transition */
    .input-transition {
        transition: border-color 0.2s, box-shadow 0.2s;
    }
</style>

<div class="min-h-screen flex flex-col md:flex-row overflow-hidden font-sans text-white bg-[#0F0F0F]">
    <!-- Left Panel -->
    <div class="w-full md:w-[45%] lg:w-[40%] min-h-screen flex flex-col p-8 md:p-12 lg:p-20 relative z-10 bg-[#0F0F0F]">
        <div class="flex-grow flex flex-col justify-center max-w-md w-full mx-auto">
            
            <!-- Brand -->
            <div class="reveal-item flex items-center gap-3 mb-8">
                <div class="bg-[#1392EC] p-2 rounded-xl shadow-lg shadow-blue-500/20 flex items-center justify-center">
                    <!-- Shield Plus Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white w-6 h-6">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                        <path d="M8 11h8"/>
                        <path d="M12 7v8"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold tracking-tight text-white">Appoint</span>
            </div>

            <!-- Headings -->
            <div class="reveal-item mb-10">
                <h1 class="text-4xl font-bold mb-3 text-white">Welcome back</h1>
                <p class="text-[#9CA3AF]">Log in using your university email.</p>
            </div>

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Email -->
                <div class="reveal-item space-y-2 relative">
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
                        <!-- Validation Tooltip -->
                        <div id="email-error" class="hidden absolute left-0 -bottom-6 text-xs text-red-500 font-medium flex items-center gap-1">
                            <span>Only @uv.edu.ph emails are allowed.</span>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="reveal-item space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            placeholder="••••••••" 
                            required
                            class="input-transition w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC] pr-12"
                        >
                        <button 
                            type="button" 
                            id="togglePassword"
                            class="hidden absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors focus:outline-none flex items-center justify-center"
                            aria-label="Toggle password visibility"
                        >
                            <span id="eye-icon" class="material-symbols-outlined eye-icon">visibility_off</span>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="reveal-item">
                    <button 
                        type="submit" 
                        id="loginBtn"
                        class="w-full py-4 bg-[#1392EC] text-white font-semibold rounded-xl transition-all flex items-center justify-center opacity-60 cursor-not-allowed"
                        disabled
                        aria-disabled="true"
                    >
                        Log In
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <nav class="reveal-item mt-8 space-y-4 text-center md:text-left">
                <div class="space-y-2">
                    <p class="text-sm text-[#9CA3AF]">
                        Don’t have an account? <a href="{{ route('register') }}" class="text-[#1392EC] hover:underline font-medium">Register</a>
                    </p>
                    <p class="text-sm text-[#9CA3AF]">
                        Clinic staff? <a href="/staff/login" class="text-[#1392EC] hover:underline font-medium">Sign in here</a>
                    </p>
                </div>
                <div class="pt-6 border-t border-white/5">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs text-gray-500 hover:text-gray-300 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12 19-7-7 7-7"/>
                            <path d="M19 12H5"/>
                        </svg>
                        Back to Home
                    </a>
                </div>
            </nav>
        </div>
    </div>

    <!-- Right Panel -->
    <aside class="hidden md:flex flex-1 items-center justify-center relative overflow-hidden bg-[#121212]">
        <!-- Grid Background -->
        <div class="absolute inset-0 opacity-20 pointer-events-none">
            <svg class="h-full w-full" preserveAspectRatio="none">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5" stroke-opacity="0.3"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>

        <!-- Glow Effect -->
        <div class="absolute w-96 h-96 bg-[#1392EC]/10 rounded-full blur-[120px] pointer-events-none"></div>

        <!-- Diamond Container -->
        <div class="w-80 h-80 grid grid-cols-2 grid-rows-2 relative z-10 transform rotate-45 border border-[#1392EC]/20">
            <!-- Cell 1: Stethoscope -->
            <div class="flex items-center justify-center border border-[#1392EC]/20 backdrop-blur-sm transition-transform hover:scale-105 duration-300">
                <div class="transform -rotate-45 text-[#1392EC] drop-shadow-[0_0_8px_rgba(19,146,236,0.3)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/>
                        <path d="M8 15v1a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6v-4"/>
                        <circle cx="20" cy="10" r="2"/>
                    </svg>
                </div>
            </div>

            <!-- Cell 2: Clipboard List -->
            <div class="flex items-center justify-center border border-[#1392EC]/20 backdrop-blur-sm transition-transform hover:scale-105 duration-300">
                <div class="transform -rotate-45 text-[#1392EC] drop-shadow-[0_0_8px_rgba(19,146,236,0.3)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                        <path d="M12 11h4"/>
                        <path d="M12 16h4"/>
                        <path d="M8 11h.01"/>
                        <path d="M8 16h.01"/>
                    </svg>
                </div>
            </div>

            <!-- Cell 3: Tooth (Dentistry) -->
            <div class="flex items-center justify-center border border-[#1392EC]/20 backdrop-blur-sm transition-transform hover:scale-105 duration-300">
                <div class="transform -rotate-45 text-[#1392EC] drop-shadow-[0_0_8px_rgba(19,146,236,0.3)]">
                    <!-- Material Symbol for Dentistry -->
                    <span class="material-symbols-outlined custom-tooth">dentistry</span>
                </div>
            </div>

            <!-- Cell 4: Activity -->
            <div class="flex items-center justify-center border border-[#1392EC]/20 backdrop-blur-sm transition-transform hover:scale-105 duration-300">
                <div class="transform -rotate-45 text-[#1392EC] drop-shadow-[0_0_8px_rgba(19,146,236,0.3)]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                </div>
            </div>
        </div>
    </aside>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Progressive Reveal ---
        const revealItems = document.querySelectorAll('.reveal-item');
        revealItems.forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('reveal-visible');
            }, index * 100); // 100ms stagger
        });

        // --- 2. Password Toggle Logic ---
        const passwordInput = document.getElementById('password');
        const toggleBtn = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eye-icon');

        if (passwordInput && toggleBtn && eyeIcon) {
            // Show/Hide button based on input length
            passwordInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    toggleBtn.classList.remove('hidden');
                } else {
                    toggleBtn.classList.add('hidden');
                }
            });

            // Toggle password visibility
            toggleBtn.addEventListener('click', function() {
                const isPassword = passwordInput.type === 'password';
                
                if (isPassword) {
                    // Switch to text (reveal)
                    passwordInput.type = 'text';
                    eyeIcon.textContent = 'visibility';
                } else {
                    // Switch to password (hide)
                    passwordInput.type = 'password';
                    eyeIcon.textContent = 'visibility_off';
                }
            });
        }

        // --- 3. Email Validation (@uv.edu.ph) & Button State ---
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('email-error');
        const loginBtn = document.getElementById('loginBtn');

        function validateEmail() {
            const value = emailInput.value.trim();
            const isMatch = value.toLowerCase().endsWith('@uv.edu.ph');
            
            // Visual Validation (only show error if not empty AND invalid)
            if (value.length > 0 && !isMatch) {
                // Invalid State
                emailInput.classList.remove('border-white/10', 'focus:ring-[#1392EC]', 'focus:border-[#1392EC]');
                emailInput.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
                emailInput.setAttribute('aria-invalid', 'true');
                emailError.classList.remove('hidden');
            } else {
                // Valid or Empty (Neutral visual)
                resetEmailState();
            }

            // Button State (Disabled if empty OR not match)
            if (isMatch) {
                loginBtn.disabled = false;
                loginBtn.removeAttribute('aria-disabled');
                loginBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                loginBtn.classList.add('hover:bg-[#1181d1]', 'active:scale-[0.98]', 'shadow-lg');
            } else {
                loginBtn.disabled = true;
                loginBtn.setAttribute('aria-disabled', 'true');
                loginBtn.classList.add('opacity-60', 'cursor-not-allowed');
                loginBtn.classList.remove('hover:bg-[#1181d1]', 'active:scale-[0.98]', 'shadow-lg');
            }
        }

        function resetEmailState() {
            emailInput.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
            emailInput.classList.add('border-white/10', 'focus:ring-[#1392EC]', 'focus:border-[#1392EC]');
            emailInput.removeAttribute('aria-invalid');
            emailError.classList.add('hidden');
        }

        if (emailInput) {
            emailInput.addEventListener('input', validateEmail);
            emailInput.addEventListener('blur', validateEmail);
            
            // Initial validation on load
            validateEmail();
        }
    });
</script>
@endsection