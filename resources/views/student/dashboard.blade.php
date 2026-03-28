@extends('layouts.app')

@section('title', 'My Dashboard')
@section('page-title', 'My Dashboard')
@section('sidebar')
    @include('partials.sidebar-student')
@endsection

@section('content')
{{-- Welcome Banner --}}
<div class="bg-gradient-to-r from-[#1392EC]/20 to-[#1392EC]/10 border border-[#1392EC]/10 rounded-2xl p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white">Welcome back, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h2>
            <p class="text-gray-400 text-sm mt-1">Here's your health overview for today, {{ now()->format('F d, Y') }}</p>
        </div>
        <a href="{{ route('student.services') }}" class="hidden md:flex items-center gap-2 px-4 py-2.5 bg-[#1392EC] hover:bg-[#1392EC] text-white text-sm font-medium rounded-xl transition-all shadow-lg shadow-[#1392EC]/20">
            <span class="material-symbols-outlined" style="font-size:18px;">add</span>
            Book Appointment
        </a>
    </div>
</div>

{{-- Quick Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Upcoming --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-400" style="font-size:20px;">event</span>
            </div>
            <span class="text-xs text-gray-500">Upcoming</span>
        </div>
        <p class="text-2xl font-bold text-white">{{ $upcomingAppointments->count() }}</p>
        <p class="text-xs text-gray-500 mt-1">Scheduled appointments</p>
    </div>

    {{-- Completed --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-[#1392EC]/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-[#1392EC]" style="font-size:20px;">check_circle</span>
            </div>
            <span class="text-xs text-gray-500">Completed</span>
        </div>
        <p class="text-2xl font-bold text-white">{{ $recentVisits->count() }}</p>
        <p class="text-xs text-gray-500 mt-1">Recent visits</p>
    </div>

    {{-- Certificates --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-400" style="font-size:20px;">description</span>
            </div>
            <span class="text-xs text-gray-500">Pending</span>
        </div>
        <p class="text-2xl font-bold text-white">{{ $pendingCertificates }}</p>
        <p class="text-xs text-gray-500 mt-1">Certificate requests</p>
    </div>

    {{-- Notifications --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-purple-400" style="font-size:20px;">notifications</span>
            </div>
            <span class="text-xs text-gray-500">Unread</span>
        </div>
        <p class="text-2xl font-bold text-white">{{ $unreadNotifications }}</p>
        <p class="text-xs text-gray-500 mt-1">Notifications</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Upcoming Appointments --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
            <h3 class="text-sm font-semibold text-white">Upcoming Appointments</h3>
            <a href="{{ route('student.appointments') }}" class="text-xs text-[#1392EC] hover:text-[#1392EC]">View All</a>
        </div>
        <div class="divide-y divide-white/5">
            @forelse($upcomingAppointments as $apt)
            <div class="px-5 py-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $apt->service->color }}15;">
                    <span class="material-symbols-outlined" style="font-size:20px; color: {{ $apt->service->color }};">medical_services</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ $apt->service->name }}</p>
                    <p class="text-xs text-gray-500">{{ $apt->date->format('M d, Y') }} · {{ \Carbon\Carbon::parse($apt->start_time)->format('g:i A') }}</p>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase
                    {{ $apt->status === 'approved' ? 'bg-[#1392EC]/10 text-[#1392EC]' : 'bg-amber-500/10 text-amber-400' }}">
                    {{ $apt->status }}
                </span>
            </div>
            @empty
            <div class="px-5 py-8 text-center">
                <span class="material-symbols-outlined text-gray-600 mb-2" style="font-size:36px;">event_busy</span>
                <p class="text-sm text-gray-500">No upcoming appointments</p>
                <a href="{{ route('student.services') }}" class="text-xs text-[#1392EC] hover:text-[#1392EC] mt-1 inline-block">Book one now →</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Announcements --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
            <h3 class="text-sm font-semibold text-white">Announcements</h3>
            <a href="{{ route('student.announcements') }}" class="text-xs text-[#1392EC] hover:text-[#1392EC]">View All</a>
        </div>
        <div class="divide-y divide-white/5">
            @forelse($announcements as $ann)
            <div class="px-5 py-4">
                <p class="text-sm font-medium text-white">{{ $ann->title }}</p>
                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit(strip_tags($ann->content), 120) }}</p>
                <p class="text-[10px] text-gray-600 mt-2">{{ $ann->published_at?->diffForHumans() }}</p>
            </div>
            @empty
            <div class="px-5 py-8 text-center">
                <span class="material-symbols-outlined text-gray-600 mb-2" style="font-size:36px;">campaign</span>
                <p class="text-sm text-gray-500">No announcements yet</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
