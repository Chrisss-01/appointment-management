@extends('layouts.onboarding')

@section('title', 'Select Program')

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
</style>

<div class="flex flex-col items-center justify-center w-full max-w-5xl mx-auto px-6 pt-12 pb-24">

    <div class="w-full mb-8 reveal-item">
        <a href="{{ route('onboarding.department') }}" class="flex items-center gap-2 text-[#64748B] hover:text-[#1392EC] transition-colors duration-200 group">
           <span class="material-symbols-outlined text-xl group-hover:-translate-x-1 transition-transform duration-200">arrow_back</span>
            <span class="text-sm font-medium">Back to Department</span>
        </a>
    </div>

    <div class="text-center mb-12 reveal-item">
        <span class="block text-xs font-bold uppercase tracking-[0.2em] text-[#64748B] mb-4">
            Complete your profile
        </span>
        <h1 class="text-4xl font-bold text-white mb-3">
            Select your Course / Program
        </h1>
        <p class="text-[#94A3B8] text-lg">
            Choose the program that matches your selected department.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full reveal-item" style="transition-delay: 100ms;">
        @foreach ($programs as $program)
            <a href="{{ route('onboarding.year-level', [
                    'department' => $department,
                    'program' => $program['code']
                ]) }}"
               class="group relative flex flex-col items-center text-center p-8 rounded-xl bg-[#1A1A1A] border border-white/10 hover:border-[#1392EC] hover:shadow-lg hover:shadow-[#1392EC]/10 transition-all duration-300 hover:-translate-y-1">

                <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center mb-4 group-hover:bg-[#1392EC]/10 transition-colors duration-300">
                    <span class="material-symbols-outlined text-[#1392EC] text-2xl group-hover:scale-110 transition-transform duration-300">
                        {{ $program['icon'] }}
                    </span>
                </div>

                <h3 class="text-xl font-bold text-white mb-1">
                    {{ $program['code'] }}
                </h3>

                <p class="text-sm text-[#94A3B8]">
                    {{ $program['name'] }}
                </p>
            </a>
        @endforeach
    </div>

    <div class="mt-16 w-full flex justify-center reveal-item" style="transition-delay: 200ms;">
        @include('partials.onboarding-progress', ['step' => 2])
    </div>

    <div class="mt-12 text-center reveal-item" style="transition-delay: 300ms;">
        <p class="text-xs font-medium text-[#64748B] uppercase tracking-widest">
            Secured Appointment System
        </p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const revealItems = document.querySelectorAll('.reveal-item');

        revealItems.forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('reveal-visible');
            }, index * 120);
        });
    });
</script>
@endsection