@extends('layouts.auth')

@section('title', 'Clinic Staff Sign In')

@section('content')
    <style>
    /* Custom Tooth Icon Styling */
    .material-symbols-outlined.custom-tooth {
        font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 48;
        font-size: 48px;
        color: #1392EC;
        user-select: none;
    }

    /* Standard Icon Styling for Grid */
    .material-symbols-outlined.grid-icon {
        font-size: 48px;
        color: #1392EC;
        opacity: 0.8;
        user-select: none;
    }

    /* Eye Icon Styling */
    .material-symbols-outlined.eye-icon {
        font-size: 20px;
        user-select: none;
    }

    /* Animation */
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

    /* Input Transition */
    .input-transition {
        transition: border-color 0.2s, box-shadow 0.2s;
    }
</style>

<div class="min-h-screen flex flex-col md:flex-row overflow-hidden font-sans text-white bg-[#0F0F0F]">
    <!-- Left Panel: Form -->
    <div class="w-full md:w-[45%] min-h-screen flex flex-col justify-center px-6 py-12 md:px-12 lg:px-20 relative z-10 bg-[#0F0F0F] overflow-y-auto">
        <div class="max-w-md w-full mx-auto">
            
            <!-- Brand -->
            <div class="reveal-item flex items-center gap-3 mb-10">
                <div class="bg-[#1392EC] p-2 rounded-xl shadow-lg shadow-blue-500/20 flex items-center justify-center">
                    <!-- Shield Plus SVG -->
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
                <h1 class="text-4xl font-bold tracking-tight text-white mb-3">Clinic Staff Sign In</h1>
                <p class="text-[#9CA3AF]">Access for authorized clinic personnel only.</p>
            </div>

            {{-- Error Alert --}}
            @if ($errors->any())
                <div id="auth-error-alert" role="alert" class="reveal-item mb-6 flex items-start gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('staff.login') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Email -->
                <div class="reveal-item space-y-2 relative">
                    <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                    <div class="relative">
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            placeholder="your.email@domain.com" 
                            value="{{ old('email') }}"
                            required
                            class="input-transition w-full bg-[#141414] border rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-white/10 focus:ring-[#1392EC] focus:border-[#1392EC]' }}"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="reveal-item space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-[#1392EC] hover:text-[#1181d1] transition-colors focus:outline-none focus:underline">
                            Forgot your password?
                        </a>
                    </div>
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
                <div class="reveal-item pt-2">
                    <button 
                        type="submit" 
                        id="loginBtn"
                        class="w-full py-4 bg-[#1392EC] text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20 active:scale-[0.98] opacity-60 cursor-not-allowed"
                        disabled
                    >
                        Sign In
                    </button>
                </div>

                <!-- Footer -->
                <div class="reveal-item mt-8 space-y-6">
                    <p class="text-xs text-gray-500 italic">
                        Staff accounts are provided by the clinic administration.
                    </p>
                    
                    <div class="pt-6 border-t border-white/5">
                        <a href="{{ url('/') }}" class="text-xs text-gray-500 hover:text-white/70 transition-colors duration-150 inline-flex items-center gap-2">
                            <span class="material-symbols-outlined text-[14px]">arrow_back</span>
                            Back to Home
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="hidden md:flex flex-1 relative overflow-hidden bg-[#121212] items-center justify-center border-l border-[#2d2d2d]">
        <!-- Background Patterns -->
        <div class="absolute inset-0 z-0 opacity-20 pointer-events-none">
            <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="none" viewBox="0 0 1000 1000">
                <path d="M-100,500 C150,300 350,700 600,400 S850,100 1100,300" fill="none" stroke="#1392EC" stroke-width="2" stroke-opacity="0.5"></path>
                <path d="M-100,600 C200,400 400,800 650,500 S900,200 1150,400" fill="none" stroke="#1392EC" stroke-width="2" stroke-opacity="0.3"></path>
                <circle cx="900" cy="200" r="30" fill="none" stroke="#1392EC" stroke-width="2" stroke-opacity="0.5"></circle>
            </svg>
        </div>

        <div class="relative z-10 flex flex-col items-center justify-center w-full p-12">
            <!-- Icon Grid -->
            <div class="grid grid-cols-2 gap-6 mb-16">
                <!-- Stethoscope -->
                <div class="w-40 h-40 rounded-2xl border border-[#1392EC]/20 bg-[#121212] flex items-center justify-center transition-transform hover:-translate-y-1 duration-300">
                    <span class="material-symbols-outlined grid-icon">stethoscope</span>
                </div>
                <!-- Clipboard -->
                <div class="w-40 h-40 rounded-2xl border border-[#1392EC]/20 bg-[#121212] flex items-center justify-center transition-transform hover:-translate-y-1 duration-300">
                    <span class="material-symbols-outlined grid-icon">assignment</span>
                </div>
                <!-- Tooth -->
                <div class="w-40 h-40 rounded-2xl border border-[#1392EC]/20 bg-[#121212] flex items-center justify-center transition-transform hover:-translate-y-1 duration-300">
                    <span class="material-symbols-outlined custom-tooth">dentistry</span>
                </div>
                <!-- Pulse -->
                <div class="w-40 h-40 rounded-2xl border border-[#1392EC]/20 bg-[#121212] flex items-center justify-center transition-transform hover:-translate-y-1 duration-300">
                    <span class="material-symbols-outlined grid-icon">pulse_alert</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Progressive Reveal
        const revealItems = document.querySelectorAll('.reveal-item');
        revealItems.forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('reveal-visible');
            }, index * 100);
        });

        // 2. Password Toggle
        const passwordInput = document.getElementById('password');
        const toggleBtn = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eye-icon');

        if (passwordInput && toggleBtn && eyeIcon) {
            passwordInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    toggleBtn.classList.remove('hidden');
                } else {
                    toggleBtn.classList.add('hidden');
                }
                validateForm(); // Re-validate on password input to enable button
            });

            toggleBtn.addEventListener('click', function() {
                const isPassword = passwordInput.type === 'password';
                if (isPassword) {
                    passwordInput.type = 'text';
                    eyeIcon.textContent = 'visibility'; // Normal eye
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.textContent = 'visibility_off'; // Slashed eye
                }
            });
        }

        // 3. Form Validation
        const emailInput = document.getElementById('email');
        const loginBtn = document.getElementById('loginBtn');

        function validateForm() {
            const hasEmail = emailInput.value.trim().length > 0;
            const hasPassword = passwordInput.value.length > 0;

            // Button State
            if (hasEmail && hasPassword) {
                loginBtn.disabled = false;
                loginBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                loginBtn.classList.add('hover:bg-[#1392EC]/90', 'active:scale-[0.98]', 'shadow-lg', 'shadow-blue-500/20');
            } else {
                loginBtn.disabled = true;
                loginBtn.classList.add('opacity-60', 'cursor-not-allowed');
                loginBtn.classList.remove('hover:bg-[#1392EC]/90', 'active:scale-[0.98]', 'shadow-lg', 'shadow-blue-500/20');
            }
        }

        if (emailInput) {
            emailInput.addEventListener('input', validateForm);
            emailInput.addEventListener('blur', validateForm);
            // Initial check
            validateForm();
        }
    });
</script>
@endsection