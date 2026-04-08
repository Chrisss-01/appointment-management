@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<style>
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

    /* Loading Spinner */
    .spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .is-loading .spinner {
        display: inline-block;
    }
    .is-loading .btn-text {
        opacity: 0.8;
    }
</style>

<div class="min-h-screen flex flex-col md:flex-row overflow-hidden font-sans text-white bg-[#0F0F0F]">
    <!-- Left Panel -->
    <div class="w-full md:w-[45%] lg:w-[40%] min-h-screen flex flex-col p-8 md:p-12 lg:p-20 relative z-10 bg-[#0F0F0F]">
        <div class="flex-grow flex flex-col justify-center max-w-md w-full mx-auto">
            
            <!-- Brand -->
            <div class="reveal-item flex items-center gap-3 mb-8">
                <div class="bg-[#1392EC] p-2 rounded-xl shadow-lg shadow-blue-500/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-2xl leading-none">lock_reset</span>
                </div>
                <span class="text-2xl font-bold tracking-tight text-white">Appoint</span>
            </div>

            <!-- Headings -->
            <div class="reveal-item mb-10">
                <h1 class="text-4xl font-bold mb-3 text-white">Reset Password</h1>
                <p class="text-[#9CA3AF] text-sm leading-relaxed">
                    Enter the email address associated with your account and we'll send you a link to reset your password.
                </p>
            </div>

            {{-- Status Alert (Success) --}}
            @if (session('status'))
                <div id="auth-status-alert" role="alert" class="reveal-item mb-6 flex items-start gap-3 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl px-4 py-3 text-sm">
                    <span class="material-symbols-outlined shrink-0 mt-0.5 text-lg">check_circle</span>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- Error Alert --}}
            @if ($errors->any())
                <div id="auth-error-alert" role="alert" class="reveal-item mb-6 flex items-start gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 text-sm">
                    <span class="material-symbols-outlined shrink-0 mt-0.5 text-lg">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('password.email') }}" method="POST" class="space-y-6" id="resetForm">
                @csrf
                
                <!-- Email -->
                <div class="reveal-item space-y-2 relative">
                    <label for="email" class="block text-sm font-medium text-gray-300">Email Address</label>
                    <div class="relative">
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            placeholder="yourname@domain.com" 
                            value="{{ old('email') }}"
                            required
                            class="input-transition w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC]"
                        >
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="reveal-item pt-2">
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="w-full py-4 bg-[#1392EC] hover:bg-[#1181d1] text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-2 active:scale-[0.98] shadow-lg shadow-blue-500/20"
                    >
                        <span class="spinner"></span>
                        <span class="btn-text">Send Reset Link</span>
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <nav class="reveal-item mt-10 text-center md:text-left">
                <div class="pt-6 border-t border-white/5">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm text-[#9CA3AF] hover:text-white transition-colors group">
                        <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
                        Back to Log In
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
        <div class="absolute w-[500px] h-[500px] bg-[#1392EC]/10 rounded-full blur-[140px] pointer-events-none"></div>

        <!-- Abstract Security Icon -->
        <div class="relative z-10 transform hover:scale-105 transition-transform duration-700">
            <div class="w-64 h-64 border border-[#1392EC]/20 rounded-full flex items-center justify-center backdrop-blur-md bg-white/5">
                <div class="w-48 h-48 border border-[#1392EC]/40 rounded-full flex items-center justify-center shadow-[0_0_40px_rgba(19,146,236,0.15)] relative">
                    <!-- Rotating rings -->
                    <svg class="absolute inset-0 w-full h-full animate-[spin_30s_linear_infinite] opacity-50" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="48" fill="none" stroke="#1392EC" stroke-width="0.5" stroke-dasharray="10 20"/>
                    </svg>
                    <div class="text-[#1392EC] drop-shadow-[0_0_15px_rgba(19,146,236,0.5)]">
                        <span class="material-symbols-outlined text-8xl" style="font-variation-settings: 'FILL' 0, 'wght' 200, 'GRAD' 0, 'opsz' 48;">mark_email_read</span>
                    </div>
                </div>
            </div>
            
            <!-- Floating connection lines -->
            <div class="absolute top-1/2 -right-20 w-32 h-[1px] bg-gradient-to-r from-[#1392EC] to-transparent opacity-50 hidden lg:block"></div>
            <div class="absolute -bottom-10 left-1/2 w-[1px] h-32 bg-gradient-to-b from-[#1392EC] to-transparent opacity-50"></div>
        </div>
    </aside>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Progressive Reveal
        const revealItems = document.querySelectorAll('.reveal-item');
        revealItems.forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('reveal-visible');
            }, index * 100); 
        });

        // Form Submission state
        const form = document.getElementById('resetForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if(form && submitBtn) {
            form.addEventListener('submit', function() {
                // Prevent multiple submissions
                if(submitBtn.disabled) return false;
                
                submitBtn.classList.add('opacity-90', 'cursor-not-allowed', 'is-loading');
                submitBtn.disabled = true;
            });
        }
    });
</script>
@endsection
