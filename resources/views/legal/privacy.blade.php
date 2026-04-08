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
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 tracking-tight">Privacy Policy</h1>
                <p class="text-slate-500 text-sm">Last Updated: April 06, 2026</p>
                <div class="h-1 w-20 bg-[#1392ec] mt-6 rounded-full mx-auto md:mx-0"></div>
            </div>

            <div class="prose prose-invert max-w-none space-y-8 text-slate-300 leading-relaxed">
                <section>
                    <h2 class="text-xl font-bold text-white flex items-center gap-3 mb-4">
                        <span class="material-symbols-outlined text-[#1392ec]">database</span>
                        1. Data We Collect
                    </h2>
                    <p>
                        To provide an efficient clinic appointment system, the UV Toledo Clinic Appointment Portal collects specific information from students, including:
                    </p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>Personal Identification:</strong> Full name, Student ID number, Department, Program, and Year Level.</li>
                        <li><strong>Contact Information:</strong> Institutional or personal email address (used for OTP and notifications).</li>
                        <li><strong>Clinical Data:</strong> Appointment reasons, service types (Medical, Dental, Vision Screening), and request details for Medical Certificates.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white flex items-center gap-3 mb-4">
                        <span class="material-symbols-outlined text-[#1392ec]">assignment_turned_in</span>
                        2. Purpose of Collection
                    </h2>
                    <p>
                        All information collected is processed in compliance with the <strong>Data Privacy Act of 2012 (RA 10173)</strong> for the following purposes:
                    </p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li>Managing and scheduling health consultations and diagnostic services.</li>
                        <li>Ensuring the authenticity of students requesting medical documentation.</li>
                        <li>Maintaining a secure medical history for continuity of clinical care.</li>
                        <li>Communicating appointment status updates and important announcements.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white flex items-center gap-3 mb-4">
                        <span class="material-symbols-outlined text-[#1392ec]">verified_user</span>
                        3. Access Control & Use
                    </h2>
                    <p>
                        The system implements strict Role-Based Access Control (RBAC) to ensure your data is only seen by authorized personnel:
                    </p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>Students:</strong> Can only access their own profile, appointments, and certificates.</li>
                        <li><strong>Clinic Staff:</strong> Can view patient profiles and clinical history to facilitate medical services.</li>
                        <li><strong>Administrators:</strong> Manage system configurations and generate anonymized reports for university statistics.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-white flex items-center gap-3 mb-4">
                        <span class="material-symbols-outlined text-[#1392ec]">security</span>
                        4. Data Protection
                    </h2>
                    <p>
                        Your privacy is our priority. Metadata and clinical records are protected through:
                    </p>
                    <ul class="list-disc pl-6 mt-4 space-y-2">
                        <li><strong>One-Time Passwords (OTP):</strong> Securing your login process.</li>
                        <li><strong>Data Encryption:</strong> Protecting sensitive records within our university servers.</li>
                        <li><strong>Secure Handling:</strong> We do not share student medical information with third-party vendors or external organizations without explicit consent.</li>
                    </ul>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
