@extends('layouts.app')
@section('title', 'Appointment Requests')
@section('page-title', 'Appointment Requests')
@section('sidebar') @include('partials.sidebar-staff') @endsection

@section('content')
{{-- Tabs --}}
<div class="flex gap-2 mb-6 overflow-x-auto">
    @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'completed' => 'Completed', 'closed' => 'Closed', 'all' => 'All'] as $key => $label)
    <a href="?status={{ $key }}" class="px-4 py-2 rounded-xl text-sm font-medium transition-all whitespace-nowrap
        {{ request('status', 'pending') === $key ? 'bg-[#1392EC] text-white' : 'bg-[#1A1A1A] text-gray-400 border border-white/5 hover:text-white' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
    @if($appointments->isEmpty())
    <div class="px-5 py-12 text-center">
        <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">inbox</span>
        <p class="text-gray-400 text-sm">No {{ request('status', 'pending') }} appointment requests</p>
    </div>
    @else
    <div class="overflow-x-auto w-full">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-gray-500 text-xs uppercase border-b border-white/5">
                    <th class="px-5 py-3 text-left font-medium">Student</th>
                    <th class="px-5 py-3 text-left font-medium">Service</th>
                    <th class="px-5 py-3 text-left font-medium">Date & Time</th>
                    <th class="px-5 py-3 text-left font-medium">Status</th>
                    <th class="px-5 py-3 text-right font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($appointments as $apt)
                @php 
                    $s = $apt->status;
                    $badge = match($s) {
                        'pending' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                        'approved' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                        'completed' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                        'no_show' => 'bg-orange-500/10 text-orange-500 border-orange-500/20',
                        'cancelled' => 'bg-red-500/10 text-red-500 border-red-500/20',
                        'cancelled_by_staff' => 'bg-red-500/10 text-red-500 border-red-500/20',
                        'rejected' => 'bg-gray-500/10 text-gray-400 border-white/5',
                        default => 'bg-gray-500/10 text-gray-400 border-white/5'
                    };
                    $statusLabel = match($s) {
                        'cancelled' => 'Cancelled by Student',
                        'cancelled_by_staff' => 'Cancelled by Staff',
                        'no_show' => 'No Show',
                        default => ucfirst($s),
                    };
                @endphp
                <tr class="hover:bg-white/[0.02] transition-colors">
                    {{-- Student --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-blue-400" style="font-size:16px;">person</span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-sm font-medium text-white">{{ $apt->student->name }}</span>
                                <p class="text-xs text-gray-500">
                                    <span class="font-mono text-gray-400">#APT-{{ $apt->id }}</span>
                                    @if($apt->student->student_id) · <span class="font-mono">{{ $apt->student->student_id }}</span> @endif
                                    @if($apt->reason) · <span class="italic" title="{{ $apt->reason }}">"{{ Str::limit($apt->reason, 30) }}"</span> @endif
                                </p>
                                @if($apt->additional_comments)
                                <p class="text-xs text-gray-500 mt-0.5 truncate max-w-[220px]"><span class="text-gray-600">Comments:</span> {{ $apt->additional_comments }}</p>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Service --}}
                    <td class="px-5 py-4 text-gray-400">{{ $apt->service->name }}</td>

                    {{-- Date & Time --}}
                    <td class="px-5 py-4">
                        <span class="text-gray-300">{{ $apt->date->format('M d, Y') }}</span><br>
                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($apt->start_time)->format('g:i A') }}</span>
                    </td>

                    {{-- Status --}}
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badge }}">
                            @if($s === 'completed')
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                            @endif
                            {{ $statusLabel }}
                        </span>
                    </td>

                    {{-- Action --}}
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($apt->status === 'pending')
                            <form action="{{ route('staff.appointments.approve', $apt) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="px-3 py-1.5 bg-[#1392EC] hover:opacity-90 text-white text-xs font-medium rounded-lg transition-all">Approve</button>
                            </form>
                            <button onclick="showRejectModal({{ $apt->id }})" class="px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-medium rounded-lg transition-all">Reject</button>
                            @elseif($apt->status === 'approved')
                            <a href="{{ route('staff.record-visits') }}{{ $apt->date->isPast() && !$apt->date->isToday() ? '?filter=missed' : '' }}" class="text-xs text-gray-400 hover:text-gray-300 transition-colors">
                                Record Visits →
                            </a>
                            <form action="{{ route('staff.appointments.no-show', $apt) }}" method="POST" onsubmit="return confirm('Mark this appointment as No Show?')">
                                @csrf @method('PATCH')
                                <button class="px-3 py-1.5 bg-orange-500/10 hover:bg-orange-500/20 text-orange-400 text-xs font-medium rounded-lg transition-all">Mark No Show</button>
                            </form>
                            @elseif(in_array($apt->status, ['cancelled', 'cancelled_by_staff', 'rejected']))
                            <button onclick="showReasonModal('{{ $statusLabel }}', '{{ addslashes($apt->status === 'rejected' ? $apt->rejection_reason : $apt->cancellation_reason) }}')" class="text-xs text-gray-400 hover:text-gray-300 transition-colors">
                                View Reason
                            </button>
                            @else
                            <span class="text-gray-600 text-xs">—</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-white/5">{{ $appointments->links() }}</div>
    @endif
</div>

@push('modals')
{{-- Reject Modal --}}
<div id="reject-modal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold text-white mb-4">Reject Appointment</h3>
        <form id="reject-form" method="POST">
            @csrf @method('PATCH')
            <label class="block text-xs text-gray-400 mb-2">Reason for rejection</label>
            <textarea name="rejection_reason" rows="3" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-red-500 resize-none" placeholder="Provide reason..."></textarea>
            <div class="flex justify-end gap-3 mt-4">
                <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white text-sm font-medium rounded-xl transition-all">Reject</button>
            </div>
        </form>
    </div>
</div>

{{-- View Reason Modal --}}
<div id="reason-modal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 w-full max-w-md">
        <h3 id="reason-modal-title" class="text-lg font-semibold text-white mb-4"></h3>
        <div class="bg-[#141414] border border-white/10 rounded-xl px-4 py-3">
            <p id="reason-modal-text" class="text-sm text-gray-300"></p>
        </div>
        <div class="flex justify-end mt-4">
            <button type="button" onclick="document.getElementById('reason-modal').classList.add('hidden')" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-sm text-gray-300 rounded-xl transition-colors">Close</button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
function showRejectModal(id) {
    document.getElementById('reject-form').action = `/staff/appointments/${id}/reject`;
    document.getElementById('reject-modal').classList.remove('hidden');
}

function showReasonModal(statusLabel, reason) {
    document.getElementById('reason-modal-title').textContent = statusLabel + ' — Reason';
    document.getElementById('reason-modal-text').textContent = reason || 'No reason provided.';
    document.getElementById('reason-modal').classList.remove('hidden');
}
</script>
@endpush
@endsection
