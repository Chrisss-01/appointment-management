@extends('layouts.app')
@section('title', 'My Availability')
@section('page-title', 'My Availability')
@section('sidebar') @include('partials.sidebar-staff') @endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Add Availability Form --}}
    <div class="lg:col-span-1">
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Add Availability Block</h3>

            <form action="{{ route('staff.availability.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Service</label>
                    <select name="service_id" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        <option value="">Select service...</option>
                        @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Date</label>
                    <input type="date" name="date" min="{{ now()->toDateString() }}" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">Start Time</label>
                        <input type="time" name="start_time" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">End Time</label>
                        <input type="time" name="end_time" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Slot Duration</label>
                    <select name="slot_duration" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        <option value="15">15 minutes</option>
                        <option value="20">20 minutes</option>
                        <option value="30">30 minutes</option>
                    </select>
                </div>

                <button type="submit" class="w-full py-3 bg-[#1392EC] hover:opacity-90 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-[#1392EC]/20">
                    Add Availability
                </button>
            </form>
        </div>
    </div>

    {{-- Availability Calendar & List --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Calendar View --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-white">Calendar View</h3>
                <select id="calendar-service-filter" class="bg-[#141414] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                    <option value="">All Services</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
            <div id="calendar" class="min-h-[400px]"></div>
        </div>

        {{-- Availability List --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/5">
                <h3 class="text-sm font-semibold text-white">My Availability Blocks</h3>
            </div>

            @if($availabilitySlots->isEmpty())
            <div class="px-5 py-12 text-center">
                <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">event_note</span>
                <p class="text-gray-400 text-sm">No availability blocks set</p>
                <p class="text-xs text-gray-500 mt-1">Add blocks to allow students to book appointments</p>
            </div>
            @else
            <div class="divide-y divide-white/5">
                @foreach($availabilitySlots as $slot)
                <div class="px-5 py-4 hover:bg-white/[0.02] transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: {{ $slot->service->color }}15;">
                                <span class="material-symbols-outlined" style="font-size:20px; color: {{ $slot->service->color }};">schedule</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white">{{ $slot->service->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $slot->date->format('M d, Y (l)') }} ·
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500">{{ $slot->generatedSlots->count() }} slots</span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-[#1392EC]/10 text-[#1392EC]">
                                {{ $slot->generatedSlots->where('status', 'available')->count() }} free
                            </span>
                            <button type="button"
                                class="text-gray-500 hover:text-red-400 transition-colors delete-availability-btn"
                                data-slot-id="{{ $slot->id }}"
                                data-check-url="{{ route('staff.availability.check-bookings', $slot) }}"
                                data-destroy-url="{{ route('staff.availability.destroy', $slot) }}"
                                data-service-name="{{ $slot->service->name }}"
                                data-slot-date="{{ $slot->date->format('M d, Y') }}">
                                <span class="material-symbols-outlined" style="font-size:18px;">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('modals')
{{-- Cancellation Warning Modal --}}
<div id="cancel-modal" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl w-full max-w-md mx-4 p-6 shadow-2xl">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-red-400" style="font-size:22px;">warning</span>
            </div>
            <h3 class="text-base font-semibold text-white">Delete Availability Block?</h3>
        </div>

        <p class="text-sm text-gray-400 mb-1" id="cancel-modal-info"></p>

        <div class="bg-red-500/5 border border-red-500/10 rounded-xl p-3 mb-4">
            <p class="text-xs text-red-400">
                <span class="font-semibold">Warning:</span> Deleting this availability block will cancel
                <span id="cancel-modal-count" class="font-bold"></span> booked appointment(s).
                Affected students will be notified of the cancellation.
            </p>
        </div>

        <form id="cancel-modal-form" method="POST">
            @csrf
            @method('DELETE')
            <div class="mb-5">
                <label class="block text-xs text-gray-400 mb-1.5">Cancellation Reason <span class="text-red-400">*</span></label>
                <textarea name="cancellation_reason" id="cancel-modal-reason" rows="3" required
                    class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-red-400 resize-none"
                    placeholder="Explain why this availability is being removed..."></textarea>
                <p id="cancel-modal-error" class="text-xs text-red-400 mt-1 hidden">Please provide a cancellation reason.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" id="cancel-modal-close"
                    class="flex-1 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-medium rounded-xl transition-colors">
                    Cancel
                </button>
                <button type="submit" id="cancel-modal-submit"
                    class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">
                    Delete & Cancel Appointments
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Simple Delete Confirmation Modal --}}
<div id="simple-delete-modal" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl w-full max-w-sm mx-4 p-6 shadow-2xl">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-gray-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-gray-400" style="font-size:22px;">delete</span>
            </div>
            <h3 class="text-base font-semibold text-white">Delete Availability Block?</h3>
        </div>
        <p class="text-sm text-gray-400 mb-5" id="simple-delete-info">Are you sure you want to delete this availability block and all its time slots?</p>
        <form id="simple-delete-form" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex items-center gap-3">
                <button type="button" id="simple-delete-close"
                    class="flex-1 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-medium rounded-xl transition-colors">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">
                    Delete
                </button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('styles')
<style>
    /* FullCalendar Dark Theme Overrides */
    .fc-theme-standard .fc-scrollgrid { border-color: rgba(255,255,255,0.05); }
    .fc-theme-standard td, .fc-theme-standard th { border-color: rgba(255,255,255,0.05); }
    .fc .fc-toolbar-title { font-size: 1.125rem; font-weight: 600; color: white; }
    .fc .fc-button-primary { background-color: #1392EC; border-color: #1392EC; text-transform: capitalize; border-radius: 0.5rem; font-size: 0.875rem; padding: 0.25rem 0.75rem; }
    .fc .fc-button-primary:not(:disabled).fc-button-active, .fc .fc-button-primary:not(:disabled):active, .fc .fc-button-primary:hover { background-color: #0d82d6; border-color: #0d82d6; }
    .fc .fc-button-primary:disabled { background-color: rgba(19, 146, 236, 0.5); border-color: transparent; }
    .fc-day-today { background-color: rgba(19, 146, 236, 0.05) !important; }
    .fc-event { cursor: pointer; border-radius: 4px; padding: 2px 4px; font-size: 0.75rem; border: none; }
    .fc-col-header-cell-cushion { color: #9ca3af; font-weight: 500; font-size: 0.75rem; text-transform: uppercase; }
    .fc-daygrid-day-number { color: #d1d5db; font-size: 0.875rem; padding: 4px !important; }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const serviceFilter = document.getElementById('calendar-service-filter');
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 550,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            themeSystem: 'standard',
            events: function(info, successCallback, failureCallback) {
                const serviceId = serviceFilter.value;
                const url = new URL('{{ route("staff.availability.calendar") }}', window.location.origin);
                if (serviceId) {
                    url.searchParams.append('service_id', serviceId);
                }
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        const events = data.map(slot => ({
                            id: slot.id,
                            title: `${slot.service_name} (${slot.available_slots} free)`,
                            start: slot.date + 'T' + slot.start_time,
                            end: slot.date + 'T' + slot.end_time,
                            backgroundColor: slot.service_color || '#1392EC',
                            borderColor: slot.service_color || '#1392EC',
                        }));
                        successCallback(events);
                    })
                    .catch(error => failureCallback(error));
            }
        });

        calendar.render();

        serviceFilter.addEventListener('change', () => {
            calendar.refetchEvents();
        });

        // ── Delete Availability Logic ─────────────────────────────────
        const cancelModal = document.getElementById('cancel-modal');
        const cancelModalForm = document.getElementById('cancel-modal-form');
        const cancelModalInfo = document.getElementById('cancel-modal-info');
        const cancelModalCount = document.getElementById('cancel-modal-count');
        const cancelModalReason = document.getElementById('cancel-modal-reason');
        const cancelModalError = document.getElementById('cancel-modal-error');
        const cancelModalClose = document.getElementById('cancel-modal-close');

        const simpleDeleteModal = document.getElementById('simple-delete-modal');
        const simpleDeleteForm = document.getElementById('simple-delete-form');
        const simpleDeleteInfo = document.getElementById('simple-delete-info');
        const simpleDeleteClose = document.getElementById('simple-delete-close');

        function openModal(modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        cancelModalClose.addEventListener('click', () => closeModal(cancelModal));
        simpleDeleteClose.addEventListener('click', () => closeModal(simpleDeleteModal));

        cancelModal.addEventListener('click', (e) => { if (e.target === cancelModal) closeModal(cancelModal); });
        simpleDeleteModal.addEventListener('click', (e) => { if (e.target === simpleDeleteModal) closeModal(simpleDeleteModal); });

        cancelModalForm.addEventListener('submit', function(e) {
            if (!cancelModalReason.value.trim()) {
                e.preventDefault();
                cancelModalError.classList.remove('hidden');
                cancelModalReason.focus();
            }
        });

        cancelModalReason.addEventListener('input', () => {
            cancelModalError.classList.add('hidden');
        });

        document.querySelectorAll('.delete-availability-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const checkUrl = this.dataset.checkUrl;
                const destroyUrl = this.dataset.destroyUrl;
                const serviceName = this.dataset.serviceName;
                const slotDate = this.dataset.slotDate;

                try {
                    const response = await fetch(checkUrl, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();

                    if (data.has_bookings) {
                        cancelModalInfo.textContent = `${serviceName} — ${slotDate}`;
                        cancelModalCount.textContent = data.booked_count;
                        cancelModalForm.action = destroyUrl;
                        cancelModalReason.value = '';
                        cancelModalError.classList.add('hidden');
                        openModal(cancelModal);
                    } else {
                        simpleDeleteInfo.textContent = `Delete the "${serviceName}" availability block on ${slotDate} and all its time slots?`;
                        simpleDeleteForm.action = destroyUrl;
                        openModal(simpleDeleteModal);
                    }
                } catch (err) {
                    if (confirm('Delete this availability block and all its slots?')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = destroyUrl;
                        form.innerHTML = `@csrf @method('DELETE')`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            });
        });
    });
</script>
@endpush
