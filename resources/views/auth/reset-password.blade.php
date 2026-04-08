@extends('layouts.auth')

@section('title', 'Set New Password')

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

    /* Eye Icon Styling */
    .material-symbols-outlined.eye-icon {
        font-size: 20px;
        user-select: none;
    }
    
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
    <div class="w-full md:w-[45%] lg:w-[40%] min-h-screen flex flex-col p-8 md:p-12 lg:p-20 relative z-10 bg-[#0F0F0F] overflow-y-auto">
        <div class="flex-grow flex flex-col justify-center max-w-md w-full mx-auto">
            
            <!-- Brand -->
            <div class="reveal-item flex items-center gap-3 mb-8">
                <div class="bg-[#1392EC] p-2 rounded-xl shadow-lg shadow-blue-500/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-2xl leading-none">key</span>
                </div>
                <span class="text-2xl font-bold tracking-tight text-white">Appoint</span>
            </div>

            <!-- Headings -->
            <div class="reveal-item mb-10">
                <h1 class="text-4xl font-bold mb-3 text-white">Set New Password</h1>
                <p class="text-[#9CA3AF] text-sm leading-relaxed">
                    Your new password must be at least 8 characters long securely protecting your account.
                </p>
            </div>

            {{-- Error Alert --}}
            @if ($errors->any())
                <div id="auth-error-alert" role="alert" class="reveal-item mb-6 flex items-start gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 text-sm">
                    <span class="material-symbols-outlined shrink-0 mt-0.5 text-lg">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('password.update') }}" method="POST" class="space-y-6" id="resetForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <!-- Email (Readonly) -->
                <div class="reveal-item space-y-2 relative">
                    <label for="email" class="block text-sm font-medium text-gray-300">Email Address</label>
                    <div class="relative">
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ $email ?? old('email') }}"
                            readonly
                            required
                            class="w-full bg-[#1A1A1A] border border-white/5 rounded-xl px-4 py-3 text-gray-400 cursor-not-allowed focus:outline-none"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="reveal-item space-y-2 relative">
                    <label for="password" class="block text-sm font-medium text-gray-300">New Password</label>
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
                            class="toggle-password hidden absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors focus:outline-none flex items-center justify-center"
                            data-target="password"
                            title="Toggle visibility"
                        >
                            <span class="material-symbols-outlined eye-icon">visibility_off</span>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="reveal-item space-y-2 relative">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirm New Password</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation" 
                            placeholder="••••••••" 
                            required
                            class="input-transition w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC] pr-12"
                        >
                        <button 
                            type="button" 
                            class="toggle-password hidden absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors focus:outline-none flex items-center justify-center"
                            data-target="password_confirmation"
                            title="Toggle visibility"
                        >
                            <span class="material-symbols-outlined eye-icon">visibility_off</span>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="reveal-item pt-4">
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="w-full py-4 bg-[#1392EC] hover:bg-[#1181d1] text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-2 active:scale-[0.98] shadow-[0_0_15px_rgba(19,146,236,0.2)] disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span class="spinner"></span>
                        <span class="btn-text">Reset Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Panel -->
    <aside class="hidden md:flex flex-1 items-center justify-center relative overflow-hidden bg-[#121212]">
        <!-- Pattern Background -->
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>

        <!-- Glow Effect -->
        <div class="absolute w-[600px] h-[600px] bg-[#1392EC]/10 rounded-full blur-[140px] pointer-events-none"></div>

        <!-- Futuristic Shield/Lock Icon -->
        <div class="relative z-10 w-80 h-80 grid gap-4 grid-cols-2 grid-rows-2 transform rotate-45 opacity-80 transition-transform duration-1000 hover:scale-105">
            <div class="border border-[#1392EC]/30 rounded-tl-3xl bg-white/5 backdrop-blur-sm shadow-[inset_0_0_20px_rgba(19,146,236,0.1)]"></div>
            <div class="border border-[#1392EC]/10 bg-black/20 backdrop-blur-sm flex items-center justify-center">
                <span class="material-symbols-outlined text-[#1392EC] transform -rotate-45 text-4xl drop-shadow-[0_0_8px_rgba(19,146,236,0.5)]">vpn_key</span>
            </div>
            <div class="border border-[#1392EC]/10 bg-black/20 backdrop-blur-sm flex items-center justify-center">
                <span class="material-symbols-outlined text-[#1392EC] transform -rotate-45 text-4xl drop-shadow-[0_0_8px_rgba(19,146,236,0.5)]">shield_locked</span>
            </div>
            <div class="border border-[#1392EC]/30 rounded-br-3xl bg-white/5 backdrop-blur-sm shadow-[inset_0_0_20px_rgba(19,146,236,0.1)]"></div>
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

        // Password Toggle Logic
        const toggleButtons = document.querySelectorAll('.toggle-password');
        
        // Show/hide toggle button based on input value
        document.getElementById('password').addEventListener('input', function() {
            const btn = document.querySelector('[data-target="password"]');
            if (this.value.length > 0) btn.classList.remove('hidden');
            else btn.classList.add('hidden');
        });
        
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const btn = document.querySelector('[data-target="password_confirmation"]');
            if (this.value.length > 0) btn.classList.remove('hidden');
            else btn.classList.add('hidden');
        });

        // Toggle action
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);
                const icon = this.querySelector('.material-symbols-outlined');
                
                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    icon.textContent = 'visibility';
                } else {
                    targetInput.type = 'password';
                    icon.textContent = 'visibility_off';
                }
            });
        });

        // Form Submission state
        const form = document.getElementById('resetForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if(form && submitBtn) {
            form.addEventListener('submit', function() {
                if(submitBtn.disabled) return false;
                
                submitBtn.classList.add('opacity-90', 'cursor-not-allowed', 'is-loading');
                submitBtn.disabled = true;
            });
        }
    });
</script>
@endsection
