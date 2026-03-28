@extends('layouts.app')

@section('title', 'My Appointments')
@section('page-title', 'My Appointments')
@section('sidebar')
    @include('partials.sidebar-student')
@endsection

@section('content')
<div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-white/5 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-white">All Appointments</h3>
        <a href="{{ route('student.services') }}" class="flex items-center gap-1.5 text-xs text-[#1392EC] hover:text-[#1392EC]">
            <span class="material-symbols-outlined" style="font-size:14px;">add</span> Book New
        </a>
    </div>

    @if($appointments->isEmpty())
    <div class="px-5 py-12 text-center">
        <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">calendar_month</span>
        <p class="text-gray-400 text-sm">You have no appointments yet</p>
        <a href="{{ route('student.services') }}" class="text-[#1392EC] text-sm hover:text-[#1392EC] mt-2 inline-block">Book your first appointment →</a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-gray-500 text-xs uppercase border-b border-white/5">
                    <th class="px-5 py-3 text-left font-medium">Service</th>
                    <th class="px-5 py-3 text-left font-medium">Date & Time</th>
                    <th class="px-5 py-3 text-left font-medium">Staff</th>
                    <th class="px-5 py-3 text-left font-medium">Queue</th>
                    <th class="px-5 py-3 text-left font-medium">Status</th>
                    <th class="px-5 py-3 text-right font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($appointments as $apt)
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: {{ $apt->service->color }}15;">
                                <span class="material-symbols-outlined" style="font-size:16px; color: {{ $apt->service->color }};">medical_services</span>
                            </div>
                            <span class="text-white font-medium">{{ $apt->service->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-400">
                        {{ $apt->date->format('M d, Y') }}<br>
                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($apt->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($apt->end_time)->format('g:i A') }}</span>
                    </td>
                    <td class="px-5 py-4 text-gray-400">{{ optional($apt->staff)->name ?? 'N/A' }}</td>
                    <td class="px-5 py-4">
                        @if($apt->queue_number)
                        <span class="px-2 py-1 rounded-lg bg-white/5 text-gray-300 text-xs font-mono">#{{ str_pad($apt->queue_number, 3, '0', STR_PAD_LEFT) }}</span>
                        @else
                        <span class="text-gray-600 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $colors = [
                                'pending' => 'bg-amber-500/10 text-amber-400',
                                'approved' => 'bg-[#1392EC]/10 text-[#1392EC]',
                                'rejected' => 'bg-red-500/10 text-red-400',
                                'completed' => 'bg-blue-500/10 text-blue-400',
                                'cancelled' => 'bg-gray-500/10 text-gray-400',
                                'cancelled_by_staff' => 'bg-red-500/10 text-red-400',
                                'no_show' => 'bg-red-500/10 text-red-400',
                            ];
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $colors[$apt->status] ?? 'bg-gray-500/10 text-gray-400' }}">
                            {{ str_replace('_', ' ', $apt->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        @if(in_array($apt->status, ['pending', 'approved']))
                        <form action="{{ route('student.appointments.cancel', $apt) }}" method="POST" onsubmit="return confirm('Cancel this appointment?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 transition-colors">Cancel</button>
                        </form>
                        @elseif($apt->status === 'rejected' && $apt->rejection_reason)
                        <span class="text-xs text-gray-500 cursor-help" title="{{ $apt->rejection_reason }}">View Reason</span>
                        @elseif($apt->status === 'cancelled_by_staff' && $apt->cancellation_reason)
                        <span class="text-xs text-red-400 cursor-help" title="{{ $apt->cancellation_reason }}">View Reason</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-white/5">
        {{ $appointments->links() }}
    </div>
    @endif
</div>
@endsection
