@extends('layouts.app')
@section('title', 'Staff Dashboard')
@section('page-title', 'Dashboard')
@section('sidebar') @include('partials.sidebar-staff') @endsection

@section('content')
{{-- Welcome --}}
<div class="bg-gradient-to-r from-[#1392EC]/20 to-[#1392EC]/10 border border-[#1392EC]/10 rounded-2xl p-6 mb-6">
    <h2 class="text-xl font-bold text-white">Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 18 ? 'Afternoon' : 'Evening') }}, {{ explode(' ', auth()->user()->name)[0] }} 👋</h2>
    <p class="text-gray-400 text-sm mt-1">{{ now()->format('l, F d, Y') }} — Here's your clinic overview</p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-[#1392EC]/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-[#1392EC]" style="font-size:20px;">today</span>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ $todayAppointments->count() }}</p>
        <p class="text-xs text-gray-500 mt-1">Today's Appointments</p>
    </div>

    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-400" style="font-size:20px;">pending</span>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ $pendingRequests }}</p>
        <p class="text-xs text-gray-500 mt-1">Pending Requests</p>
    </div>

    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-400" style="font-size:20px;">check_circle</span>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ $todayCompleted }}</p>
        <p class="text-xs text-gray-500 mt-1">Completed Today</p>
    </div>

    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-purple-400" style="font-size:20px;">task</span>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ $pendingTasks }}</p>
        <p class="text-xs text-gray-500 mt-1">Pending Tasks</p>
    </div>

    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-400" style="font-size:20px;">verified</span>
            </div>
        </div>
        <p class="text-2xl font-bold text-white">{{ $pendingCertificateRequests }}</p>
        <p class="text-xs text-gray-500 mt-1">Certificate Requests</p>
    </div>
</div>

{{-- Today's queue --}}
<div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-white/5 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-white">Today's Appointment Queue</h3>
        <a href="{{ route('staff.appointments') }}" class="text-xs text-[#1392EC] hover:opacity-80">View All</a>
    </div>
    @if($todayAppointments->isEmpty())
    <div class="px-5 py-12 text-center">
        <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">event_available</span>
        <p class="text-gray-400 text-sm">No appointments scheduled for today</p>
    </div>
    @else
    <div class="divide-y divide-white/5">
        @foreach($todayAppointments as $apt)
        <div class="px-5 py-4 flex items-center gap-4 hover:bg-white/[0.02] transition-colors">
            <div class="text-center shrink-0">
                <span class="text-lg font-bold text-white">#{{ str_pad($apt->queue_number ?? 0, 2, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: {{ $apt->service->color }}15;">
                <span class="material-symbols-outlined" style="font-size:16px; color: {{ $apt->service->color }};">medical_services</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white">{{ $apt->student->name }}</p>
                <p class="text-xs text-gray-500">{{ $apt->service->name }} · {{ \Carbon\Carbon::parse($apt->start_time)->format('g:i A') }}</p>
            </div>
            @php $c = ['pending'=>'bg-amber-500/10 text-amber-400','approved'=>'bg-[#1392EC]/10 text-[#1392EC]','completed'=>'bg-blue-500/10 text-blue-400','no_show'=>'bg-red-500/10 text-red-400']; @endphp
            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $c[$apt->status] ?? 'bg-gray-500/10 text-gray-400' }}">{{ $apt->status }}</span>

            @if($apt->status === 'approved')
            <form action="{{ route('staff.appointments.complete', $apt) }}" method="POST">
                @csrf @method('PATCH')
                <button class="px-3 py-1.5 bg-[#1392EC] hover:opacity-90 text-white text-xs font-medium rounded-lg transition-all">Complete</button>
            </form>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
