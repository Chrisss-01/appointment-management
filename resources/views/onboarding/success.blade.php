@extends('layouts.onboarding')

@section('title', 'Profile Complete')

@section('content')
<style>
    /* Custom Glow for Success Icon */
    .success-glow {
        box-shadow: 0 0 40px -5px rgba(19, 146, 236, 0.25);
    }

    /* Progressive Reveal Animation */
    .reveal-item {
        opacity: 0;
        transform: translateY(12px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        will-change: opacity, transform;
    }
    .reveal-visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* Loading Dots Animation */
    @keyframes pulse-blue {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.85); }
    }
    .dot-animate {
        animation: pulse-blue 1.4s infinite ease-in-out;
    }
</style>

<div class="flex flex-col items-center justify-center w-full min-h-[calc(100vh-100px)] px-6 relative">

    <!-- Main Content Container -->
    <div class="flex flex-col items-center text-center z-10 max-w-lg mx-auto -mt-10">
        
        <!-- Success Icon -->
        <div class="reveal-item mb-10 relative">
            <div class="w-24 h-24 rounded-full border-2 border-[#1392EC] flex items-center justify-center success-glow bg-[#1392EC]/5">
                <span class="material-symbols-outlined text-[#1392EC] text-[40px]" style="font-variation-settings: 'wght' 600;">check</span>
            </div>
        </div>

        <!-- Heading -->
        <h1 class="reveal-item text-4xl md:text-5xl font-bold text-white mb-4 tracking-tight" style="transition-delay: 100ms;">
            You're all set!
        </h1>

        <!-- Subtitle -->
        <p class="reveal-item text-[#94A3B8] text-lg leading-relaxed mb-12" style="transition-delay: 200ms;">
            Your student profile is complete. Taking you to your home page...
        </p>

        <!-- Loading Dots -->
        <div class="reveal-item flex gap-2.5 mb-10" style="transition-delay: 300ms;">
            <div class="w-2.5 h-2.5 rounded-full bg-[#1392EC] dot-animate"></div>
            <div class="w-2.5 h-2.5 rounded-full bg-[#1392EC] dot-animate" style="animation-delay: 0.2s;"></div>
            <div class="w-2.5 h-2.5 rounded-full bg-[#1392EC] dot-animate" style="animation-delay: 0.4s;"></div>
        </div>

        <!-- Fallback Link -->
        <p class="reveal-item text-xs text-[#64748B]" style="transition-delay: 400ms;">
            If you are not redirected, go to 
            <a href="{{ route('student.dashboard') }}" class="text-[#94A3B8] hover:text-white underline decoration-[#94A3B8]/30 hover:decoration-white/50 underline-offset-4 transition-all duration-200">Home</a>
        </p>

    </div>

    <!-- Footer Note -->
    <div class="absolute bottom-12 text-center reveal-item" style="transition-delay: 500ms;">
        <p class="text-xs font-medium text-[#64748B] uppercase tracking-widest">Secured Appointment System</p>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Reveal Animation
        const revealItems = document.querySelectorAll('.reveal-item');
        revealItems.forEach((item) => {
            item.classList.add('reveal-visible');
        });

        // 2. Redirect Logic
        setTimeout(() => {
            // Redirect to dashboard after 2 seconds
            window.location.href = "{{ route('student.dashboard') }}";
        }, 2000);
    });
</script>
@endsection