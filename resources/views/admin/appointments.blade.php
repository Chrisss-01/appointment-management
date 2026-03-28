@extends('layouts.app')
@section('title', 'All Appointments')
@section('page-title', 'All Appointments')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
{{-- Filter Tabs --}}
<div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2 scrollbar-hide">
    @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'completed' => 'Completed', 'no_show' => 'No Show', 'cancelled' => 'Cancelled', 'rejected' => 'Rejected'] as $val => $label)
    <a href="{{ route('admin.appointments', ['status' => $val]) }}" 
       class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition-all {{ $status === $val ? 'bg-[#1392EC] text-white shadow-lg shadow-[#1392EC]/20' : 'bg-[#1A1A1A] border border-white/5 text-gray-400 hover:text-white hover:bg-white/5' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
    @if($appointments->isEmpty())
    <div class="px-5 py-12 text-center">
        <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">event_busy</span>
        <p class="text-gray-400 text-sm">No appointments found</p>
    </div>
    @else
    <div class="overflow-x-auto w-full">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-white/5 text-xs text-gray-400">
                <tr>
                    <th class="px-5 py-4 font-medium rounded-tl-xl">Date & Time</th>
                    <th class="px-5 py-4 font-medium">Student</th>
                    <th class="px-5 py-4 font-medium">Service</th>
                    <th class="px-5 py-4 font-medium">Staff</th>
                    <th class="px-5 py-4 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($appointments as $apt)
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex flex-col">
                            <span class="text-white font-medium">{{ $apt->date->format('M d, Y') }}</span>
                            <span class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($apt->start_time)->format('g:i A') }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-[#1392EC]/10 flex items-center justify-center text-[#1392EC] text-xs font-bold">
                                {{ strtoupper(substr($apt->student->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-gray-200">{{ $apt->student->name ?? 'Unknown Student' }}</span>
                                <span class="text-gray-500 text-xs">{{ $apt->student->student_id ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-300">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" style="background: {{ $apt->service->color ?? '#1392EC' }};"></span>
                            {{ $apt->service->name ?? 'Unknown Service' }}
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-300">
                        {{ $apt->staff->name ?? 'Unknown Staff' }}
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $s = $apt->status;
                            $badge = match($s) {
                                'pending' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                'approved' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                'completed' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                'no_show' => 'bg-orange-500/10 text-orange-500 border-orange-500/20',
                                'cancelled' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                'rejected' => 'bg-gray-500/10 text-gray-400 border-white/5',
                                default => 'bg-gray-500/10 text-gray-400 border-white/5'
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badge }} w-fit">
                            @if($s === 'completed')
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                            @endif
                            {{ str_replace('_', ' ', $s) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-white/5">
        {{ $appointments->links() }}
    </div>
    @endif
</div>
@endsection
