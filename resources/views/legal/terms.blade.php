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

        <!-- Content Card -->
        <div class="bg-[#1A1A1A] border border-white/5 rounded-3xl p-8 md:p-12 shadow-2xl">
            <div class="mb-10 text-center md:text-left">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 tracking-tight">Terms of Service</h1>
                <p class="text-slate-500 text-sm">Last Updated: April 06, 2026</p>
                <div class="h-1 w-20 bg-[#1392ec] mt-6 rounded-full mx-auto md:mx-0"></div>
            </div>

            <div class="prose prose-invert max-w-none space-y-8 text-slate-300 leading-relaxed">
                <p>Welcome to the <strong>UV Toledo Clinic Appointment Portal</strong>. By using this service, you agree to comply with the following terms and responsibilities.</p>

                <section>
                    <h2 class="text-xl font-bold text-white flex items-center gap-3 mb-4">
                        <span class="material-symbols-outlined text-[#1392ec]">face</span>
                        1. User Responsibilities
                    </h2>
                    <p>
                        As a user of the appointment system, you are responsible for:
                    </p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>Accurate Information:</strong> Providing truthful and complete information during registration and appointment booking.</li>
                        <li><strong>Proper Usage:</strong> Using the portal only for its intended purpose of medical consultations and health-related requests.</li>
                        <li><strong>Account Security:</strong> Protecting your registration email and OTP information to prevent unauthorized access to your health records.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white flex items-center gap-3 mb-4">
                        <span class="material-symbols-outlined text-[#1392ec]">calendar_today</span>
                        2. Appointment Policies
                    </h2>
                    <p>
                        To ensure efficient clinical service for all students, the following policies apply:
                    </p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>Attendance:</strong> Students must arrive on time for their scheduled consultation.</li>
                        <li><strong>Cancellations:</strong> Appointments should be cancelled or rescheduled at least 24 hours in advance to make the slot available for other students.</li>
                        <li><strong>Late Arrivals:</strong> Late arrivals may result in a shorter consultation or a rescheduled appointment depending on the clinic's load.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white flex items-center gap-3 mb-4">
                        <span class="material-symbols-outlined text-[#1392ec]">shield_person</span>
                        3. Administrative Authority
                    </h2>
                    <p>
                        The UV Toledo Clinic and its administrators maintain full control over the system's management, including:
                    </p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>Slot Management:</strong> Staff may adjust available slots and durations based on clinic availability.</li>
                        <li><strong>Request Verification:</strong> All medical certificate requests are subject to clinical review and document integrity checks.</li>
                        <li><strong>Communication:</strong> The clinic may use our notification systems to inform you of any changes regarding your appointments.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white flex items-center gap-3 mb-4">
                        <span class="material-symbols-outlined text-[#1392ec]">report_gmailerrorred</span>
                        4. Consequences of Misuse
                    </h2>
                    <p>
                        Any attempt to manipulate the system, provide fraudulent health records, or disrupt clinic operations may result in:
                    </p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li>Temporary or permanent suspension of your portal access.</li>
                        <li>Referral to the university's disciplinary committee for further investigation.</li>
                        <li>Revocation of any certificates obtained through fraudulent means.</li>
                    </ul>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
