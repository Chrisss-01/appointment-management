@extends('layouts.landing')

@section('content')
<div class="py-20 bg-[#0F0F0F] min-h-screen relative overflow-hidden">
    <!-- background glow -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[300px] bg-[#1392ec]/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-4xl mx-auto px-6 relative z-10">
        <!-- Navigation Back -->
        <div class="mb-10">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-[#1392ec] transition-colors group">
                <span class="material-symbols-outlined text-sm group-hover:-translate-x-1 transition-transform">arrow_back</span>
                <span class="text-sm font-medium tracking-wide border-b border-transparent group-hover:border-[#1392ec]">Return to Landing</span>
            </a>
        </div>

        <!-- Header Card -->
        <div class="bg-[#1A1A1A] border border-white/5 rounded-3xl p-8 md:p-12 shadow-2xl mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 tracking-tight">Clinic Schedule</h1>
                    <p class="text-slate-400 max-w-md">Our standard operating hours and typical service availability for UV Toledo students.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Standard Operating Hours -->
                <div class="space-y-6">
                    <h2 class="text-xl font-bold text-white flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#1392ec]">schedule</span>
                        Standard Clinic Hours
                    </h2>
                    <p class="text-slate-400 text-sm italic">Service provided by the school nurse.</p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5">
                            <span class="text-slate-300 font-medium">Monday — Thursday</span>
                            <span class="text-white font-bold">8:00 AM — 6:00 PM</span>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5">
                            <span class="text-slate-300 font-medium">Friday</span>
                            <span class="text-white font-bold">8:00 AM — 5:00 PM</span>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5 opacity-50">
                            <span class="text-slate-300 font-medium">Saturday — Sunday</span>
                            <span class="text-rose-500 font-bold uppercase text-xs tracking-widest">Closed</span>
                        </div>
                    </div>
                    
                    <div class="p-4 rounded-2xl bg-[#1392ec]/5 border border-[#1392ec]/10 flex gap-4">
                        <span class="material-symbols-outlined text-[#1392ec]">info</span>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            <strong>Note:</strong> Standard hours represent the nurse's primary availability. The clinic may be temporarily closed if the nurse is attending school meetings or medical emergencies.
                        </p>
                    </div>
                </div>

                <!-- Typical Service Schedule -->
                <div class="space-y-6">
                    <h2 class="text-xl font-bold text-white flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#1392ec]">medical_services</span>
                        Typical Service Schedule
                    </h2>
                    <p class="text-slate-400 text-sm">Days when specific specialists are usually available.</p>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <!-- Dental -->
                        <div class="p-4 rounded-2xl bg-[#1392ec]/10 border border-[#1392ec]/20">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-[#1392ec] text-sm">dentistry</span>
                                <h3 class="text-white font-bold tracking-tight">Dental Consultation</h3>
                            </div>
                            <p class="text-slate-300 text-sm mb-1">Typically available: <span class="text-white font-semibold">Tuesdays & Thursdays</span></p>
                        </div>

                        <!-- Medical -->
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-slate-400 text-sm">stethoscope</span>
                                <h3 class="text-white font-bold tracking-tight">Other Medical Checks</h3>
                            </div>
                            <p class="text-slate-300 text-sm mb-1">Typically available: <span class="text-white font-semibold">Mondays, Wednesdays, Fridays</span></p>
                        </div>
                    </div>

                    <div class="p-6 bg-[#252525] border border-white/5 rounded-2xl">
                        <h4 class="text-white font-bold mb-3 text-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#1392ec] text-[18px]">calendar_month</span>
                            Plan your visit
                        </h4>
                        <p class="text-slate-400 text-sm leading-relaxed mb-4">
                            All consultations are scheduled to avoid conflict as the clinic operates in a unified examination space.
                        </p>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-[#1392ec] font-bold text-sm group">
                            Book an appointment
                            <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">chevron_right</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
