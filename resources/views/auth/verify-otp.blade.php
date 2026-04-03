@extends('layouts.auth')

@section('title', 'Verify Email')

@section('content')
<style>
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

    .otp-input {
        letter-spacing: 12px;
        text-indent: 12px;
        font-size: 28px;
        font-weight: 700;
        text-align: center;
    }

    .otp-input::placeholder {
        letter-spacing: 8px;
        font-size: 20px;
        font-weight: 400;
    }
</style>

<div class="min-h-screen flex flex-col lg:flex-row overflow-hidden font-sans text-white bg-[#0F0F0F]">
    <!-- Left Panel: OTP Form -->
    <div class="w-full lg:w-[45%] min-h-screen flex flex-col justify-center px-6 py-12 md:px-12 lg:px-20 relative z-10 bg-[#0F0F0F]">
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
            <div class="reveal-item mb-2">
                <h1 class="text-3xl font-bold tracking-tight text-white mb-2">Verify your email</h1>
                <p class="text-[#9CA3AF]">We sent a verification code to your university email. Please check your inbox.</p>
            </div>

            <!-- Email display -->
            <div class="reveal-item mb-8">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#141414] border border-white/10 text-sm text-[#9CA3AF]">
                    <span class="material-symbols-outlined text-[18px] text-[#1392EC]">mail</span>
                    <span>{{ $email }}</span>
                </div>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="reveal-item reveal-visible mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Success Message -->
            @if(session('success'))
                <div class="reveal-item reveal-visible mb-4 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- OTP Form -->
            <form action="{{ route('otp.verify') }}" method="POST" class="space-y-6">
                @csrf

                <div class="reveal-item space-y-2">
                    <label for="otp" class="block text-sm font-medium text-gray-300">Verification Code</label>
                    <input
                        type="text"
                        name="otp"
                        id="otp"
                        maxlength="6"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        autocomplete="one-time-code"
                        placeholder="000000"
                        required
                        autofocus
                        class="otp-input input-transition w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC]"
                    >
                </div>

                <!-- Verify Button -->
                <div class="reveal-item">
                    <button
                        type="submit"
                        id="verifyBtn"
                        class="w-full py-3.5 bg-[#1392EC] text-white font-bold rounded-xl transition-all flex items-center justify-center gap-2 hover:bg-[#1180d4] opacity-60 cursor-not-allowed"
                        disabled
                    >
                        Verify
                    </button>
                </div>
            </form>

            <!-- Resend OTP -->
            <div class="reveal-item mt-6 flex items-center justify-between">
                <p class="text-sm text-[#9CA3AF]">Didn't receive the code?</p>
                <form action="{{ route('otp.resend') }}" method="POST" id="resendForm">
                    @csrf
                    <button
                        type="submit"
                        id="resendBtn"
                        class="text-sm font-medium text-[#1392EC] hover:text-[#1180d4] transition-colors disabled:text-gray-600 disabled:cursor-not-allowed"
                    >
                        Resend Code
                    </button>
                </form>
            </div>

            <!-- Timer -->
            <div id="resendTimer" class="hidden mt-2 text-xs text-[#9CA3AF] text-right">
                Resend available in <span id="countdown" class="font-medium text-white">30</span>s
            </div>

            <!-- Back to Register -->
            <div class="reveal-item mt-8 text-center">
                <a href="{{ route('register') }}" class="text-sm text-[#9CA3AF] hover:text-white transition-colors">
                    ← Back to Registration
                </a>
            </div>
        </div>
    </div>

    <!-- Right Panel: Decorative -->
    <div class="hidden lg:flex w-[55%] relative overflow-hidden items-center justify-center bg-[#0A0A0A]">
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 40px, white 40px, white 41px), repeating-linear-gradient(90deg, transparent, transparent 40px, white 40px, white 41px);"></div>

        <div class="relative z-10 flex flex-col items-center gap-8 px-12 max-w-lg text-center">
            <!-- Shield icon with verification check -->
            <div class="relative">
                <div class="w-32 h-32 rounded-3xl bg-gradient-to-br from-[#1392EC]/20 to-[#1392EC]/5 flex items-center justify-center border border-[#1392EC]/20 shadow-[0_0_60px_-15px_rgba(19,146,236,0.3)]">
                    <span class="material-symbols-outlined text-[64px] text-[#1392EC]">verified_user</span>
                </div>
                <div class="absolute -bottom-2 -right-2 w-10 h-10 rounded-xl bg-[#1392EC] flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <span class="material-symbols-outlined text-[20px] text-white">mail</span>
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-white mb-3">Email Verification</h2>
                <p class="text-[#9CA3AF] leading-relaxed">We're verifying your university email to ensure only authorized students can access the clinic appointment system.</p>
            </div>

            <!-- Steps -->
            <div class="flex flex-col gap-4 text-left w-full">
                <div class="flex items-center gap-4 p-4 rounded-xl bg-white/5 border border-white/5">
                    <div class="w-8 h-8 rounded-lg bg-[#1392EC]/20 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-[#1392EC]">1</span>
                    </div>
                    <p class="text-sm text-[#9CA3AF]">Check your <span class="text-white font-medium">@uv.edu.ph</span> inbox</p>
                </div>
                <div class="flex items-center gap-4 p-4 rounded-xl bg-white/5 border border-white/5">
                    <div class="w-8 h-8 rounded-lg bg-[#1392EC]/20 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-[#1392EC]">2</span>
                    </div>
                    <p class="text-sm text-[#9CA3AF]">Enter the <span class="text-white font-medium">6-digit code</span> from the email</p>
                </div>
                <div class="flex items-center gap-4 p-4 rounded-xl bg-white/5 border border-white/5">
                    <div class="w-8 h-8 rounded-lg bg-[#1392EC]/20 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-[#1392EC]">3</span>
                    </div>
                    <p class="text-sm text-[#9CA3AF]">Complete your <span class="text-white font-medium">profile setup</span></p>
                </div>
            </div>
        </div>

        <!-- Top-right curves -->
        <svg class="absolute top-0 right-0 w-[500px] h-[500px] opacity-[0.04]" viewBox="0 0 500 500">
            <circle cx="500" cy="0" r="200" fill="none" stroke="#1392EC" stroke-width="0.5"/>
            <circle cx="500" cy="0" r="300" fill="none" stroke="#1392EC" stroke-width="0.5"/>
            <circle cx="500" cy="0" r="400" fill="none" stroke="#1392EC" stroke-width="0.5"/>
        </svg>

        <!-- Bottom-left curves -->
        <svg class="absolute bottom-0 left-0 w-[400px] h-[400px] opacity-[0.04]" viewBox="0 0 400 400">
            <circle cx="0" cy="400" r="150" fill="none" stroke="#1392EC" stroke-width="0.5"/>
            <circle cx="0" cy="400" r="250" fill="none" stroke="#1392EC" stroke-width="0.5"/>
            <circle cx="0" cy="400" r="350" fill="none" stroke="#1392EC" stroke-width="0.5"/>
        </svg>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Progressive reveal animation
    const items = document.querySelectorAll('.reveal-item');
    items.forEach((el, i) => {
        setTimeout(() => el.classList.add('reveal-visible'), 80 + i * 80);
    });

    // OTP input — allow only digits
    const otpInput = document.getElementById('otp');
    const verifyBtn = document.getElementById('verifyBtn');

    otpInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
        const valid = this.value.length === 6;
        verifyBtn.disabled = !valid;
        verifyBtn.classList.toggle('opacity-60', !valid);
        verifyBtn.classList.toggle('cursor-not-allowed', !valid);
    });

    // Resend cooldown timer
    const resendBtn = document.getElementById('resendBtn');
    const resendTimer = document.getElementById('resendTimer');
    const countdown = document.getElementById('countdown');

    function startCooldown(seconds) {
        resendBtn.disabled = true;
        resendTimer.classList.remove('hidden');
        let remaining = seconds;
        countdown.textContent = remaining;

        const interval = setInterval(() => {
            remaining--;
            countdown.textContent = remaining;
            if (remaining <= 0) {
                clearInterval(interval);
                resendBtn.disabled = false;
                resendTimer.classList.add('hidden');
            }
        }, 1000);
    }

    // Start cooldown after successful resend
    @if(session('success'))
        startCooldown(30);
    @endif

    // Also start cooldown on form submit to prevent double-clicks
    document.getElementById('resendForm').addEventListener('submit', function () {
        startCooldown(30);
    });
});
</script>
@endsection
