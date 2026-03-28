@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Analytics Dashboard')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-400" style="font-size:20px;">school</span>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ $totalStudents }}</p>
        <p class="text-xs text-gray-500 mt-1">Registered Students</p>
    </div>
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-[#1392EC]/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-[#1392EC]" style="font-size:20px;">medical_services</span>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ $todayAppointments }}</p>
        <p class="text-xs text-gray-500 mt-1">Today's Appointments</p>
    </div>
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-400" style="font-size:20px;">pending_actions</span>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ $pendingAppointments }}</p>
        <p class="text-xs text-gray-500 mt-1">Pending Approvals</p>
    </div>
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-purple-400" style="font-size:20px;">trending_up</span>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ $monthlyAppointments }}</p>
        <p class="text-xs text-gray-500 mt-1">This Month</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Weekly Trend Chart --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-4">Weekly Appointment Trend</h3>
        <div class="space-y-3">
            @foreach($weeklyTrend as $day)
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 w-12 shrink-0">{{ $day['date'] }}</span>
                <div class="flex-1 bg-white/5 rounded-full h-6 overflow-hidden relative">
                    @if($day['count'] > 0)
                    <div class="h-full bg-[#1392EC]/30 rounded-full flex items-center px-2 transition-all" style="width: {{ min(($day['count'] / max($weeklyTrend->max('count'), 1)) * 100, 100) }}%">
                        <span class="text-[10px] text-[#1392EC] font-bold">{{ $day['count'] }}</span>
                    </div>
                    @endif
                </div>
                <span class="text-[10px] text-gray-600 w-8 text-right">{{ $day['completed'] }}✓</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Service Distribution --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-4">Service Distribution (This Month)</h3>
        <div class="space-y-4">
            @foreach($serviceDistribution as $svc)
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded" style="background: {{ $svc['color'] }};"></span>
                        <span class="text-sm text-gray-300">{{ $svc['name'] }}</span>
                    </div>
                    <span class="text-sm font-semibold text-white">{{ $svc['count'] }}</span>
                </div>
                <div class="w-full bg-white/5 rounded-full h-2">
                    <div class="h-full rounded-full transition-all" style="width: {{ $serviceDistribution->sum('count') > 0 ? ($svc['count'] / $serviceDistribution->sum('count')) * 100 : 0 }}%; background: {{ $svc['color'] }};"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
