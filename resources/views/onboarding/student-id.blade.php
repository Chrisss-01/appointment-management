@extends('layouts.onboarding')

@section('title', 'Enter Student ID')

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
        <a href="{{ route('onboarding.year-level', ['department' => $department, 'program' => $program]) }}" class="flex items-center gap-2 text-[#64748B] hover:text-[#1392EC] transition-colors duration-200 group">
            <span class="material-symbols-outlined text-xl group-hover:-translate-x-1 transition-transform duration-200">arrow_back</span>
            <span class="text-sm font-medium">Back to Year Level</span>
        </a>
    </div>

    <!-- Header Block -->
    <div class="text-center mt-16 mb-12 reveal-item" style="transition-delay: 100ms;">
        <span class="block text-xs font-bold uppercase tracking-[0.2em] text-[#64748B] mb-4">Complete your profile</span>
        <h1 class="text-4xl font-bold text-white mb-3">Enter your Student ID</h1>
        <p class="text-[#94A3B8] text-lg">Your Student ID helps the clinic verify your university enrollment.</p>
    </div>

    <!-- Input Section -->
    <div class="w-full max-w-md mx-auto mb-20 reveal-item" style="transition-delay: 200ms;">
        <form id="studentIdForm" action="{{ route('onboarding.complete') }}" method="POST">
            @csrf
            <input type="hidden" name="department" value="{{ $department }}">
            <input type="hidden" name="program" value="{{ $program }}">
            <input type="hidden" name="year_level" value="{{ $yearLevel }}">
            
            @if($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="mb-8">
                <label for="student_id" class="block text-sm font-medium text-[#CBD5E1] mb-2 ml-1">Student ID Number</label>
                <input 
                    type="text" 
                    id="student_id" 
                    name="student_id" 
                    placeholder="2024-00000" 
                    class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-5 py-4 text-white placeholder-[#475569] focus:outline-none focus:ring-2 focus:ring-[#1392EC] focus:border-transparent transition-all duration-200"
                    required
                >
                <p class="mt-2 text-xs text-[#64748B] ml-1">Enter the ID number assigned by the university.</p>
            </div>

            <button 
                type="submit" 
                class="w-full bg-[#1392EC] hover:bg-[#1182D1] text-white font-bold py-4 rounded-xl shadow-lg shadow-[#1392EC]/20 hover:shadow-[#1392EC]/30 active:scale-[0.99] transition-all duration-200"
            >
                Complete Profile
            </button>
        </form>
    </div>

    <!-- Progress Indicator -->
    <div class="w-full flex justify-center reveal-item" style="transition-delay: 300ms;">
        @include('partials.onboarding-progress', ['step' => 4])
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
    const studentIdForm = document.getElementById('studentIdForm');

    // Remove preventDefault script, let browser submit

</script>
@endsection