@extends('layouts.app')

@section('title', 'My Appointments')
@section('page-title', 'My Appointments')
@section('sidebar')
    @include('partials.sidebar-student')
@endsection

@section('content')
<div x-data="{ cancelId: null, cancelAction: '', viewReason: '', viewReasonTitle: '' }">

    {{-- Filter Tabs --}}
    <div class="flex gap-2 mb-6 overflow-x-auto">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'completed' => 'Completed', 'no_show' => 'No Show', 'closed' => 'Closed'] as $key => $label)
        <a href="?status={{ $key }}" class="px-4 py-2 rounded-xl text-sm font-medium transition-all whitespace-nowrap
            {{ ($status ?? 'all') === $key ? 'bg-[#1392EC] text-white' : 'bg-[#1A1A1A] text-gray-400 border border-white/5 hover:text-white' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-white/5 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-white">
                {{ ($status ?? 'all') === 'all' ? 'All Appointments' : ucwords(str_replace('_', ' ', $status)) . ' Appointments' }}
            </h3>
            <a href="{{ route('student.services') }}" class="flex items-center gap-1.5 text-xs text-[#1392EC] hover:text-[#1392EC]">
                <span class="material-symbols-outlined" style="font-size:14px;">add</span> Book New
            </a>
        </div>

        @if($appointments->isEmpty())
        <div class="px-5 py-12 text-center">
            <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">calendar_month</span>
            <p class="text-gray-400 text-sm">No {{ ($status ?? 'all') === 'all' ? '' : strtolower(str_replace('_', ' ', $status)) . ' ' }}appointments found</p>
            @if(($status ?? 'all') === 'all')
            <a href="{{ route('student.services') }}" class="text-[#1392EC] text-sm hover:text-[#1392EC] mt-2 inline-block">Book your first appointment →</a>
            @endif
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
                                @php
                                    $serviceIcons = [
                                        'medical-consultation'         => 'stethoscope',
                                        'dental-consultation'          => 'dentistry',
                                        'medical-certificate-request'  => 'description',
                                    ];
                                    $serviceIcon = $serviceIcons[$apt->service->slug] ?? 'medical_services';
                                @endphp
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: {{ $apt->service->color }}15;">
                                    <span class="material-symbols-outlined" style="font-size:16px; color: {{ $apt->service->color }};">{{ $serviceIcon }}</span>
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
                                $statusColors = [
                                    'pending' => 'bg-amber-500/10 text-amber-400',
                                    'approved' => 'bg-[#1392EC]/10 text-[#1392EC]',
                                    'rejected' => 'bg-red-500/10 text-red-400',
                                    'completed' => 'bg-green-500/10 text-green-400',
                                    'cancelled' => 'bg-gray-500/10 text-gray-400',
                                    'cancelled_by_staff' => 'bg-red-500/10 text-red-400',
                                    'no_show' => 'bg-red-500/10 text-red-400',
                                ];
                                $statusLabels = [
                                    'pending' => 'Pending',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled by You',
                                    'cancelled_by_staff' => 'Cancelled by Staff',
                                    'no_show' => 'No Show',
                                ];
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $statusColors[$apt->status] ?? 'bg-gray-500/10 text-gray-400' }}">
                                {{ $statusLabels[$apt->status] ?? ucfirst($apt->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if($apt->status === 'pending')
                                <button type="button"
                                    @click="cancelId = {{ $apt->id }}; cancelAction = '{{ route('student.appointments.cancel', $apt) }}'"
                                    class="text-xs text-red-400 hover:text-red-300 transition-colors">
                                    Cancel Appointment
                                </button>
                            @elseif($apt->status === 'completed')
                                <a href="{{ route('student.health') }}" class="text-xs text-[#1392EC] hover:text-[#1392EC]/80 transition-colors">
                                    View Medical Record
                                </a>
                            @elseif(in_array($apt->status, ['cancelled', 'cancelled_by_staff']) && $apt->cancellation_reason)
                                <button type="button"
                                    @click="viewReason = {{ json_encode($apt->cancellation_reason) }}; viewReasonTitle = 'Cancellation Reason'"
                                    class="text-xs text-gray-400 hover:text-gray-300 transition-colors">
                                    View Reason
                                </button>
                            @elseif($apt->status === 'rejected' && $apt->rejection_reason)
                                <button type="button"
                                    @click="viewReason = {{ json_encode($apt->rejection_reason) }}; viewReasonTitle = 'Rejection Reason'"
                                    class="text-xs text-gray-400 hover:text-gray-300 transition-colors">
                                    View Reason
                                </button>
                            @else
                                <span class="text-gray-600 text-xs">—</span>
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

    {{-- Cancel Appointment Modal --}}
    <div x-show="cancelId !== null" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="cancelId = null">
        <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 w-full max-w-md"
             @click.outside="cancelId = null">
            <h3 class="text-lg font-semibold text-white mb-1">Cancel Appointment</h3>
            <p class="text-sm text-gray-400 mb-4">Are you sure you want to cancel this appointment? Please select a reason.</p>

            <form :action="cancelAction" method="POST" x-data="{ selectedReason: '', customReason: '' }">
                @csrf
                @method('PATCH')

                <label class="block text-xs text-gray-400 mb-2">Reason for cancellation <span class="text-red-400">*</span></label>
                <select x-model="selectedReason"
                    class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC] mb-3">
                    <option value="">Select a reason</option>
                    <option value="Wrong booking">Wrong booking</option>
                    <option value="Schedule conflict">Schedule conflict</option>
                    <option value="Feeling better">Feeling better</option>
                    <option value="__other__">Other</option>
                </select>

                <div x-show="selectedReason === '__other__'" x-cloak class="mb-3">
                    <label class="block text-xs text-gray-400 mb-2">Please specify</label>
                    <textarea x-model="customReason" rows="3"
                        class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none"
                        placeholder="Enter your reason..."></textarea>
                </div>

                {{-- Hidden field that combines the reason --}}
                <input type="hidden" name="cancellation_reason"
                    :value="selectedReason === '__other__' ? customReason : selectedReason">

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" @click="cancelId = null"
                        class="px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">
                        Go Back
                    </button>
                    <button type="submit"
                        :disabled="!selectedReason || (selectedReason === '__other__' && !customReason.trim())"
                        class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white text-sm font-medium rounded-xl transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                        Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- View Reason Modal --}}
    <div x-show="viewReason !== ''" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="viewReason = ''; viewReasonTitle = ''">
        <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 w-full max-w-md"
             @click.outside="viewReason = ''; viewReasonTitle = ''">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gray-500/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-gray-400" style="font-size:20px;">info</span>
                </div>
                <h3 class="text-lg font-semibold text-white" x-text="viewReasonTitle"></h3>
            </div>

            <div class="bg-[#141414] rounded-xl p-4 mb-4">
                <p class="text-sm text-gray-300" x-text="viewReason"></p>
            </div>

            <div class="flex justify-end">
                <button type="button" @click="viewReason = ''; viewReasonTitle = ''"
                    class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white text-sm font-medium rounded-xl transition-all">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
