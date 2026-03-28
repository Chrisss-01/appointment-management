@extends('layouts.onboarding')

@section('title', 'Select Year Level')

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

<div class="flex flex-col items-center justify-center w-full max-w-5xl mx-auto px-6 pt-12 pb-24 relative">
    
    <!-- Back Link -->
    <div class="absolute top-12 left-6 md:left-12 reveal-item">
        <a href="{{ route('onboarding.program', ['department' => $department]) }}" class="flex items-center gap-2 text-[#64748B] hover:text-[#1392EC] transition-colors duration-200 group">
            <span class="material-symbols-outlined text-xl group-hover:-translate-x-1 transition-transform duration-200">arrow_back</span>
            <span class="text-sm font-medium">Back to Course/Program</span>
        </a>
    </div>

    <!-- Header Block -->
    <div class="text-center mt-16 mb-20 reveal-item" style="transition-delay: 100ms;">
        <span class="block text-xs font-bold uppercase tracking-[0.2em] text-[#64748B] mb-4">Complete your profile</span>
        <h1 class="text-4xl font-bold text-white mb-3">Select your Year Level</h1>
        <p class="text-[#94A3B8] text-lg">Choose your current year to continue with your profile setup.</p>
    </div>

    <!-- Horizontal Selector -->
    <div class="w-full max-w-3xl mx-auto mb-24 reveal-item" style="transition-delay: 200ms;">
        <div class="relative">
            <!-- Horizontal Line -->
            <div class="absolute top-[14px] left-0 w-full h-[1px] bg-[#334155] z-0"></div>

            <!-- Steps Container -->
            <div class="relative z-10 flex justify-between items-start w-full px-4 md:px-12">
                @php
                    // Determine year levels based on department
                    $levels = isset($yearLevels) ? $yearLevels : (
                        ($department === 'shs') 
                        ? [
                            ['value' => '11', 'label' => 'Grade 11'],
                            ['value' => '12', 'label' => 'Grade 12']
                        ] 
                        : [
                            ['value' => '1', 'label' => '1st Year'],
                            ['value' => '2', 'label' => '2nd Year'],
                            ['value' => '3', 'label' => '3rd Year'],
                            ['value' => '4', 'label' => '4th Year']
                        ]
                    );
                @endphp

                @foreach($levels as $level)
                <a href="{{ route('onboarding.student-id', ['department' => $department, 'program' => $program, 'year_level' => $level['value']]) }}" class="group flex flex-col items-center gap-4 cursor-pointer focus:outline-none">
                    <!-- Circle Indicator -->
                    <div class="relative flex items-center justify-center w-8 h-8">
                        <!-- Hover Glow (Invisible by default) -->
                        <div class="absolute inset-0 bg-[#1392EC]/20 rounded-full blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <!-- The Circle -->
                        <div class="w-7 h-7 rounded-full bg-[#0F0F0F] border-2 border-[#475569] group-hover:border-[#1392EC] group-hover:scale-110 transition-all duration-300 z-10"></div>
                        
                        <!-- Active State (Conceptual - if we were showing current selection, but this is a selection page) -->
                        <!-- For this selection page, all are 'inactive' until clicked/hovered -->
                    </div>
                    
                    <!-- Label -->
                    <span class="text-sm font-medium text-[#64748B] group-hover:text-white transition-colors duration-300">{{ $level['label'] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Progress Indicator -->
    <div class="w-full flex justify-center reveal-item" style="transition-delay: 300ms;">
        @include('partials.onboarding-progress', ['step' => 3])
    </div>

    <!-- Footer Note -->
    <div class="mt-12 text-center reveal-item" style="transition-delay: 400ms;">
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
