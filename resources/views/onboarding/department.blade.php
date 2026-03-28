@extends('layouts.onboarding')

@section('title', 'Select Department')

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
</style>

<div class="flex flex-col items-center justify-center w-full max-w-5xl mx-auto px-6 pt-12 pb-24">
    
    <!-- Header Block -->
    <div class="text-center mb-12 reveal-item">
        <span class="block text-xs font-bold uppercase tracking-[0.2em] text-[#64748B] mb-4">Complete your profile</span>
        <h1 class="text-4xl font-bold text-white mb-3">Select your Department</h1>
        <p class="text-[#94A3B8] text-lg">This helps us connect you with the right clinic services.</p>
    </div>

    <!-- Department Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full reveal-item" style="transition-delay: 100ms;">
        
        <!-- COED -->
        <a href="{{ route('onboarding.program', ['department' => 'coed']) }}" class="group relative flex flex-col items-center text-center p-8 rounded-xl bg-[#1A1A1A] border border-white/10 hover:border-[#1392EC] transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-[#1392EC]/10">
            <div class="w-12 h-12 rounded-full bg-[#262626] flex items-center justify-center mb-4 group-hover:bg-[#1392EC]/10 transition-colors duration-300">
                <span class="material-symbols-outlined text-[#1392EC] text-2xl group-hover:scale-110 transition-transform duration-300">school</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-1">COED</h3>
            <p class="text-sm text-[#94A3B8]">College of Education</p>
        </a>

        <!-- CBA -->
        <a href="{{ route('onboarding.program', ['department' => 'cba']) }}" class="group relative flex flex-col items-center text-center p-8 rounded-xl bg-[#1A1A1A] border border-white/10 hover:border-[#1392EC] transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-[#1392EC]/10">
            <div class="w-12 h-12 rounded-full bg-[#262626] flex items-center justify-center mb-4 group-hover:bg-[#1392EC]/10 transition-colors duration-300">
                <span class="material-symbols-outlined text-[#1392EC] text-2xl group-hover:scale-110 transition-transform duration-300">corporate_fare</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-1">CBA</h3>
            <p class="text-sm text-[#94A3B8]">College of Business Administration</p>
        </a>

        <!-- CETA -->
        <a href="{{ route('onboarding.program', ['department' => 'ceta']) }}" class="group relative flex flex-col items-center text-center p-8 rounded-xl bg-[#1A1A1A] border border-white/10 hover:border-[#1392EC] transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-[#1392EC]/10">
            <div class="w-12 h-12 rounded-full bg-[#262626] flex items-center justify-center mb-4 group-hover:bg-[#1392EC]/10 transition-colors duration-300">
                <span class="material-symbols-outlined text-[#1392EC] text-2xl group-hover:scale-110 transition-transform duration-300">architecture</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-1">CETA</h3>
            <p class="text-sm text-[#94A3B8]">College of Engineering, Technology & Arch.</p>
        </a>

        <!-- CCJE -->
        <a href="{{ route('onboarding.program', ['department' => 'ccje']) }}" class="group relative flex flex-col items-center text-center p-8 rounded-xl bg-[#1A1A1A] border border-white/10 hover:border-[#1392EC] transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-[#1392EC]/10">
            <div class="w-12 h-12 rounded-full bg-[#262626] flex items-center justify-center mb-4 group-hover:bg-[#1392EC]/10 transition-colors duration-300">
                <span class="material-symbols-outlined text-[#1392EC] text-2xl group-hover:scale-110 transition-transform duration-300">gavel</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-1">CCJE</h3>
            <p class="text-sm text-[#94A3B8]">College of Criminal Justice Education</p>
        </a>

        <!-- SHS -->
        <a href="{{ route('onboarding.program', ['department' => 'shs']) }}" class="group relative flex flex-col items-center text-center p-8 rounded-xl bg-[#1A1A1A] border border-white/10 hover:border-[#1392EC] transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-[#1392EC]/10">
            <div class="w-12 h-12 rounded-full bg-[#262626] flex items-center justify-center mb-4 group-hover:bg-[#1392EC]/10 transition-colors duration-300">
                <span class="material-symbols-outlined text-[#1392EC] text-2xl group-hover:scale-110 transition-transform duration-300">menu_book</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-1">SHS</h3>
            <p class="text-sm text-[#94A3B8]">Senior High School</p>
        </a>

        <!-- OTHER DEPARTMENTS (Disabled) -->
        <div class="flex flex-col items-center text-center p-8 rounded-xl bg-[#1A1A1A]/50 border border-white/5 opacity-50 cursor-not-allowed">
            <div class="w-12 h-12 rounded-full bg-[#262626] flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[#64748B] text-2xl">more_horiz</span>
            </div>
            <h3 class="text-xl font-bold text-[#64748B] mb-1">OTHER DEPARTMENTS</h3>
            <p class="text-[10px] font-bold uppercase tracking-widest text-[#64748B]">Coming Soon</p>
        </div>

    </div>

    <!-- Progress Indicator -->
    <div class="mt-16 w-full flex justify-center reveal-item" style="transition-delay: 200ms;">
        @include('partials.onboarding-progress', ['step' => 1])
    </div>

    <!-- Footer Note -->
    <div class="mt-12 text-center reveal-item" style="transition-delay: 300ms;">
        <p class="text-xs font-medium text-[#64748B] uppercase tracking-widest">Secured Appointment System</p>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const revealItems = document.querySelectorAll('.reveal-item');
        revealItems.forEach((item) => {
            item.classList.add('reveal-visible');
        });
    });
</script>
@endsection
