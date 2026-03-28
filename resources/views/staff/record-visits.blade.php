@extends('layouts.app')
@section('title', 'Record Visits')
@section('page-title', 'Record Visits')
@section('sidebar') @include('partials.sidebar-staff') @endsection

@section('content')
<div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-white/5">
        <h3 class="text-sm font-semibold text-white">Today's Approved Appointments</h3>
        <p class="text-xs text-gray-500 mt-0.5">Mark appointments as completed after the visit</p>
    </div>
    @if($appointments->isEmpty())
    <div class="px-5 py-12 text-center">
        <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">event_available</span>
        <p class="text-gray-400 text-sm">No approved appointments to record today</p>
    </div>
    @else
    <div class="divide-y divide-white/5">
        @foreach($appointments as $apt)
        <div class="px-5 py-4 flex items-center gap-4 hover:bg-white/[0.02] transition-colors">
            <div class="text-center shrink-0 w-10">
                <span class="text-lg font-bold text-white">#{{ str_pad($apt->queue_number ?? 0, 2, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white">{{ $apt->student->name }}</p>
                <p class="text-xs text-gray-500">{{ $apt->service->name }} · {{ \Carbon\Carbon::parse($apt->start_time)->format('g:i A') }}</p>
            </div>
            <form action="{{ route('staff.appointments.complete', $apt) }}" method="POST">
                @csrf @method('PATCH')
                <button class="px-4 py-2 bg-[#1392EC] hover:opacity-90 text-white text-xs font-medium rounded-lg transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined" style="font-size:16px;">check</span>
                    Complete
                </button>
            </form>
            <form action="{{ route('staff.appointments.no-show', $apt) }}" method="POST" onsubmit="return confirm('Mark as no-show?')">
                @csrf @method('PATCH')
                <button class="px-3 py-2 bg-white/5 hover:bg-white/10 text-gray-400 text-xs font-medium rounded-lg transition-all">No Show</button>
            </form>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
