@extends('layouts.app')

@section('title', 'Book Appointment - ' . $service->name)
@section('page-title', 'Book Appointment')
@section('sidebar')
    @include('partials.sidebar-student')
@endsection

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('student.services') }}" class="text-gray-400 hover:text-white transition-colors">
        <span class="material-symbols-outlined" style="font-size:20px;">arrow_back</span>
    </a>
    <div>
        <h2 class="text-lg font-bold text-white">{{ $service->name }}</h2>
        <p class="text-sm text-gray-500">Select a date and time slot to book your appointment</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
    {{-- Calendar --}}
    <div class="lg:col-span-3 bg-[#1A1A1A] border border-white/5 rounded-2xl p-6 lg:self-start">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-semibold text-white" id="calendar-month"></h3>
            <div class="flex gap-2">
                <button id="prev-month" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-all">
                    <span class="material-symbols-outlined" style="font-size:18px;">chevron_left</span>
                </button>
                <button id="next-month" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center text-gray-400 hover:text-white transition-all">
                    <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
                </button>
            </div>
        </div>

        {{-- Day headers --}}
        <div class="grid grid-cols-7 gap-1 mb-2">
            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
            <div class="text-center text-xs text-gray-500 font-medium py-2">{{ $day }}</div>
            @endforeach
        </div>

        {{-- Calendar grid --}}
        <div id="calendar-grid" class="grid grid-cols-7 gap-1"></div>

        <div class="mt-4 flex items-center gap-3 text-xs text-gray-500">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-green-500"></span> Available
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-gray-700"></span> No slots
            </div>
        </div>
    </div>

    {{-- Time Slots Panel --}}
    <div class="lg:col-span-2">
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-1" id="slots-title">Select a Date</h3>
            <p id="slots-subtitle" class="text-xs text-gray-500 mb-4">Choose a highlighted date from the calendar</p>

            <div id="slots-container" class="space-y-2 max-h-80 overflow-y-auto">
                <div class="text-center py-8 text-gray-600">
                    <span class="material-symbols-outlined mb-2" style="font-size:40px;">event_note</span>
                    <p class="text-sm">Pick a date to see available times</p>
                </div>
            </div>

            {{-- Reason --}}
            <div id="booking-form" class="hidden mt-4 pt-4 border-t border-white/5">
                <label class="block text-xs text-gray-400 mb-2">Reason for visit (optional)</label>
                @if(isset($reasonPresets) && $reasonPresets->count())
                <select id="reason-preset" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC] mb-2" onchange="toggleCustomReason(this)">
                    <option value="">Select a reason</option>
                    @foreach($reasonPresets as $preset)
                    <option value="{{ $preset->label }}">{{ $preset->label }}</option>
                    @endforeach
                    <option value="__other__">Other (specify below)</option>
                </select>
                @endif
                <textarea id="booking-reason" rows="2" class="w-full bg-[#141414] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC] resize-none {{ isset($reasonPresets) && $reasonPresets->count() ? 'hidden' : '' }}" placeholder="Briefly describe your concern..."></textarea>

                <div class="mt-3">
                    <label class="block text-xs text-gray-400 mb-2">Additional Comments <span class="text-gray-600">(optional)</span></label>
                    <textarea id="booking-additional-comments" rows="3" class="w-full bg-[#141414] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC] resize-none" placeholder="Any extra details about your concern..."></textarea>
                </div>

                <button id="confirm-booking" class="w-full mt-3 py-3 bg-[#1392EC] hover:bg-[#1392EC] text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-[#1392EC]/20 flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                    <span class="material-symbols-outlined" style="font-size:18px;">check_circle</span>
                    Confirm Booking
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const serviceId = {{ $service->id }};
    const serviceColor = '{{ $service->color }}';
    let currentDate = new Date();
    let availableDates = {};
    let selectedSlotId = null;

    const calendarGrid = document.getElementById('calendar-grid');
    const monthLabel = document.getElementById('calendar-month');
    const slotsContainer = document.getElementById('slots-container');
    const slotsTitle = document.getElementById('slots-title');
    const slotsSubtitle = document.getElementById('slots-subtitle');
    const bookingForm = document.getElementById('booking-form');
    const confirmBtn = document.getElementById('confirm-booking');

    // Load available dates
    async function loadAvailableDates() {
        try {
            const res = await fetch(`/student/services/${serviceId}/available-dates`);
            const dates = await res.json();
            availableDates = {};
            dates.forEach(d => { availableDates[d.date] = d.slot_count; });
            renderCalendar();
        } catch(e) { console.error(e); }
    }

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        monthLabel.textContent = `${months[month]} ${year}`;

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today = new Date();
        today.setHours(0,0,0,0);

        let html = '';
        for (let i = 0; i < firstDay; i++) {
            html += `<div class="h-12"></div>`;
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const dateObj = new Date(year, month, d);
            const isPast = dateObj < today;
            const hasSlots = availableDates[dateStr];
            const isToday = dateObj.getTime() === today.getTime();

            let classes = 'h-12 rounded-lg flex items-center justify-center text-sm font-medium transition-all cursor-default relative group';

            if (isPast) {
                classes += ' text-gray-700';
            } else if (hasSlots) {
                classes += ' cursor-pointer hover:scale-105 text-white font-semibold';
                html += `<div class="${classes}" style="background:${serviceColor}25; border: 1px solid ${serviceColor}40;" onclick="selectDate('${dateStr}')" data-date="${dateStr}">
                    ${d}
                    <span class="absolute bottom-1.5 left-1/2 -translate-x-1/2 w-1.5 h-1.5 rounded-full bg-green-500 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                </div>`;
                continue;
            } else {
                classes += ' text-gray-600 hover:bg-white/5';
                if (isToday) classes += ' ring-1 ring-[#1392EC]/30';
            }
            html += `<div class="${classes}">${d}</div>`;
        }

        calendarGrid.innerHTML = html;
    }

    // Global function for onclick
    window.selectDate = async function(dateStr) {
        selectedSlotId = null;
        bookingForm.classList.add('hidden');
        confirmBtn.disabled = true;

        slotsTitle.textContent = new Date(dateStr + 'T00:00').toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric' });
        slotsSubtitle.textContent = 'Loading available slots...';
        slotsContainer.innerHTML = '<div class="text-center py-4"><div class="w-6 h-6 border-2 border-[#1392EC] border-t-transparent rounded-full animate-spin mx-auto"></div></div>';

        // Highlight selected date
        calendarGrid.querySelectorAll('[data-date]').forEach(el => {
            el.style.outline = el.dataset.date === dateStr ? `2px solid ${serviceColor}` : 'none';
            el.style.outlineOffset = '2px';
        });

        try {
            const res = await fetch(`/student/services/${serviceId}/available-slots?date=${dateStr}`);
            const slots = await res.json();

            if (slots.length === 0) {
                slotsSubtitle.textContent = 'No available slots';
                slotsContainer.innerHTML = '<div class="text-center py-6 text-gray-600"><p class="text-sm">All slots are taken</p></div>';
                return;
            }

            slotsSubtitle.textContent = `${slots.length} slot(s) available`;
            slotsContainer.innerHTML = slots.map(s => `
                <button onclick="selectSlot(${s.id}, this)" class="slot-btn w-full flex items-center gap-3 px-4 py-3 rounded-xl border border-white/10 bg-[#141414] hover:border-[#1392EC]/30 hover:bg-[#1392EC]/5 transition-all text-left">
                    <span class="material-symbols-outlined text-[#1392EC]" style="font-size:18px;">schedule</span>
                    <div class="flex-1">
                        <span class="text-sm font-medium text-white">${formatTime(s.start_time)} – ${formatTime(s.end_time)}</span>
                        <span class="block text-xs text-gray-500">${s.staff_name}</span>
                    </div>
                    <span class="material-symbols-outlined text-gray-600 check-icon" style="font-size:18px;">radio_button_unchecked</span>
                </button>
            `).join('');
        } catch(e) {
            slotsContainer.innerHTML = '<div class="text-center py-6 text-red-400"><p class="text-sm">Error loading slots</p></div>';
        }
    };

    window.selectSlot = function(slotId, el) {
        selectedSlotId = slotId;
        document.querySelectorAll('.slot-btn').forEach(btn => {
            btn.classList.remove('border-[#1392EC]/50', 'bg-[#1392EC]/10');
            btn.querySelector('.check-icon').textContent = 'radio_button_unchecked';
        });
        el.classList.add('border-[#1392EC]/50', 'bg-[#1392EC]/10');
        el.querySelector('.check-icon').textContent = 'check_circle';
        bookingForm.classList.remove('hidden');
        confirmBtn.disabled = false;
    };

    function formatTime(time) {
        const [h, m] = time.split(':');
        const hour = parseInt(h);
        return `${hour > 12 ? hour - 12 : hour}:${m} ${hour >= 12 ? 'PM' : 'AM'}`;
    }

    // Confirm booking
    confirmBtn?.addEventListener('click', async () => {
        if (!selectedSlotId) return;
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';

        try {
            const res = await fetch('/student/appointments/book', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    generated_slot_id: selectedSlotId,
                    reason: getBookingReason(),
                    additional_comments: document.getElementById('booking-additional-comments')?.value || null,
                }),
            });

            const data = await res.json();
            if (data.success) {
                slotsContainer.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-14 h-14 rounded-full bg-[#1392EC]/10 flex items-center justify-center mx-auto mb-3">
                            <span class="material-symbols-outlined text-[#1392EC]" style="font-size:28px;">check_circle</span>
                        </div>
                        <p class="text-sm font-semibold text-white">Booking Confirmed!</p>
                        <p class="text-xs text-gray-400 mt-1">${data.message}</p>
                        <a href="/student/appointments" class="inline-block mt-4 text-xs text-[#1392EC] hover:text-[#1392EC]">View My Appointments →</a>
                    </div>`;
                bookingForm.classList.add('hidden');
                loadAvailableDates();
            } else {
                alert(data.message || 'Booking failed');
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<span class="material-symbols-outlined" style="font-size:18px;">check_circle</span> Confirm Booking';
            }
        } catch(e) {
            alert('Something went wrong. Please try again.');
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<span class="material-symbols-outlined" style="font-size:18px;">check_circle</span> Confirm Booking';
        }
    });

    document.getElementById('prev-month')?.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        loadAvailableDates();
    });
    document.getElementById('next-month')?.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        loadAvailableDates();
    });

    loadAvailableDates();
})();

function toggleCustomReason(select) {
    const textarea = document.getElementById('booking-reason');
    if (select.value === '__other__') {
        textarea.classList.remove('hidden');
        textarea.value = '';
        textarea.focus();
    } else {
        textarea.classList.add('hidden');
    }
}

function getBookingReason() {
    const presetSelect = document.getElementById('reason-preset');
    const textarea = document.getElementById('booking-reason');
    if (presetSelect && presetSelect.value && presetSelect.value !== '__other__') {
        return presetSelect.value;
    }
    return textarea.value;
}
</script>
@endpush
@endsection
