@extends('layouts.app')
@section('title', 'My Availability')
@section('page-title', 'My Availability')
@section('sidebar') @include('partials.sidebar-staff') @endsection

@section('content')
{{--
    LAYOUT: relative container â†’ calendar (full-width, never resizes)
                               â†’ absolute drawer (slides over calendar from right)
    Uses transform:translateX only â€” zero layout reflow, GPU-accelerated.
--}}
<div x-data="availabilityManager()" x-init="init()" class="relative overflow-hidden rounded-2xl">

    {{-- â”€â”€ Calendar Card (always full-width) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">

        {{-- Card Header --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                {{-- Dynamic heading --}}
                <h3 class="text-sm font-semibold text-white" x-text="activeView === 'list' ? 'Upcoming Availability' : 'Calendar View'"></h3>
                <span x-show="activeView !== 'list' && !panelOpen" x-cloak
                    class="text-[11px] text-gray-600 flex items-center gap-1">
                    <span class="material-symbols-outlined" style="font-size:13px;">touch_app</span>
                    Click a date to manage
                </span>
            </div>
            <div class="flex items-center gap-3">
                {{-- Custom view switcher removed, now integrated into FullCalendar header --}}
                <select id="calendar-service-filter" @change="refetchCalendar()" x-show="activeView !== 'list'"
                    class="bg-[#141414] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                    <option value="">All Services</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
                <button @click="quickAdd()" id="quick-add-btn"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-[#1392EC] hover:bg-[#1392EC]/80 active:scale-95 text-white text-xs font-semibold rounded-xl transition-all duration-150 shadow-lg shadow-[#1392EC]/20">
                    <span class="material-symbols-outlined" style="font-size:16px;">add</span>
                    Quick Add
                </button>
            </div>
        </div>

        {{-- Calendar Element (toolbar always visible, harness hidden via CSS in list view) --}}
        <div id="calendar" :class="`view-${activeView}`"></div>

        {{-- ── LIST VIEW ──────────────────────────────────────────────────── --}}
        <div x-show="activeView === 'list'" x-cloak class="mt-4">

            {{-- List Loading skeleton --}}
            <div x-show="listLoading" x-cloak class="space-y-4">
                <div class="h-6 w-32 bg-white/5 rounded-lg animate-pulse"></div>
                <div class="space-y-2">
                    <div class="h-16 bg-white/[0.04] rounded-xl animate-pulse"></div>
                    <div class="h-16 bg-white/[0.04] rounded-xl animate-pulse opacity-70"></div>
                </div>
                <div class="h-6 w-24 bg-white/5 rounded-lg animate-pulse"></div>
                <div class="h-16 bg-white/[0.04] rounded-xl animate-pulse"></div>
            </div>

            {{-- Empty state --}}
            <div x-show="!listLoading && listGroups.length === 0" x-cloak
                 class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-16 h-16 rounded-2xl bg-white/[0.04] flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-gray-600" style="font-size:32px;">event_available</span>
                </div>
                <p class="text-sm font-medium text-gray-500">No upcoming availability</p>
                <p class="text-xs text-gray-600 mt-1">Use Quick Add or click a calendar date to create one</p>
            </div>

            {{-- Grouped list --}}
            <div x-show="!listLoading && listGroups.length > 0" x-cloak class="space-y-6">
                <template x-for="group in listGroups" :key="group.date">
                    <div>
                        {{-- Date heading --}}
                        <div class="flex items-center gap-3 mb-2.5">
                            <h4 class="text-xs font-bold text-gray-400" x-text="group.date_label"></h4>
                            <span class="text-[10px] text-gray-600" x-text="group.date_formatted"></span>
                            <div class="flex-1 h-px bg-white/5"></div>
                        </div>

                        {{-- Slot rows --}}
                        <div class="space-y-2">
                            <template x-for="slot in group.slots" :key="slot.id">
                                <div class="group flex items-center gap-3 bg-[#111] border border-white/[0.06] rounded-xl px-4 py-3 hover:border-white/10 transition-colors duration-150">

                                    {{-- Colored dot indicator --}}
                                    <div class="w-2.5 h-2.5 rounded-full shrink-0" :style="`background:${slot.service_color}`"></div>

                                    {{-- Time --}}
                                    <div class="w-28 shrink-0">
                                        <p class="text-xs font-semibold text-white" x-text="slot.start_time"></p>
                                        <p class="text-[10px] text-gray-600" x-text="'– ' + slot.end_time"></p>
                                    </div>

                                    {{-- Service + slot counts --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-white truncate" x-text="slot.service_name"></p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[10px] text-gray-600" x-text="slot.total_slots + ' slots'"></span>
                                            <span class="text-[10px] text-[#1392EC]" x-text="slot.free_slots + ' free'"></span>
                                            {{-- Booked badge --}}
                                            <span x-show="slot.has_bookings" x-cloak
                                                class="text-[10px] px-1.5 py-0.5 bg-amber-500/10 border border-amber-500/20 text-amber-400 rounded-full"
                                                x-text="slot.booked_slots + ' booked'"></span>
                                        </div>
                                    </div>

                                    {{-- Action buttons (visible on hover) --}}
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                        <button @click="openEditModal(slot)" title="Edit"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-500 hover:text-white hover:bg-white/8 transition-all duration-150">
                                            <span class="material-symbols-outlined" style="font-size:15px;">edit</span>
                                        </button>
                                        <button @click="deleteTimeslot(slot)" title="Delete"
                                            :disabled="deleting === slot.id"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-all duration-150 disabled:opacity-40">
                                            <span class="material-symbols-outlined" style="font-size:15px;"
                                                  x-text="deleting === slot.id ? 'hourglass_empty' : 'delete'"></span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- â”€â”€ Backdrop (blocks calendar interaction when drawer is open) â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    <div id="drawer-backdrop"
         @click="closePanel()"
         class="absolute inset-0 rounded-2xl z-10 pointer-events-none"
         style="background: rgba(0,0,0,0); transition: background 250ms ease;"
         :class="panelOpen ? '!pointer-events-auto' : ''">
    </div>

    {{-- â”€â”€ Absolute Drawer â€” slides in from right, never resizes calendar â”€â”€â”€â”€â”€ --}}
    <div id="availability-drawer"
         class="absolute top-0 right-0 h-full bg-[#1A1A1A] border-l border-white/5 z-20 flex flex-col"
         style="width: 380px; will-change: transform; transform: translateX(100%); transition: transform 260ms cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden;">

        {{-- Drawer Header --}}
        <div class="px-5 py-4 border-b border-white/5 flex items-center justify-between shrink-0 bg-[#1A1A1A]">
            <div>
                <p class="text-[10px] text-[#1392EC] uppercase tracking-widest font-semibold">Selected Date</p>
                <h3 class="text-base font-semibold text-white mt-0.5 leading-tight" x-text="selectedDateFormatted">â€”</h3>
            </div>
            <button @click="closePanel()"
                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/8 rounded-lg transition-all duration-150">
                <span class="material-symbols-outlined" style="font-size:20px;">close</span>
            </button>
        </div>

        {{-- Drawer Body (independently scrollable) --}}
        <div class="flex-1 overflow-y-auto" id="panel-scroll-area">

            {{-- Existing Blocks Section --}}
            <div class="px-5 pt-5 pb-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Availability Blocks</h4>
                    <span x-show="!loading && timeslots.length > 0" x-cloak
                          class="text-[10px] text-gray-600 bg-white/5 rounded-full px-2 py-0.5"
                          x-text="timeslots.length + (timeslots.length === 1 ? ' block' : ' blocks')"></span>
                </div>

                {{-- Skeleton --}}
                <div x-show="loading" x-cloak class="space-y-2.5">
                    <div class="h-[72px] bg-white/[0.04] rounded-xl animate-pulse"></div>
                    <div class="h-[72px] bg-white/[0.04] rounded-xl animate-pulse opacity-60"></div>
                </div>

                {{-- Empty State --}}
                <div x-show="!loading && timeslots.length === 0" x-cloak
                     class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-12 h-12 rounded-xl bg-white/[0.04] flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-gray-600" style="font-size:24px;">event_busy</span>
                    </div>
                    <p class="text-sm text-gray-500">No blocks on this date</p>
                    <p class="text-xs text-gray-600 mt-1">Use the form below to add one</p>
                </div>

                {{-- Timeslot List --}}
                <div x-show="!loading && timeslots.length > 0" x-cloak class="space-y-2">
                    <template x-for="slot in timeslots" :key="slot.id">
                        <div class="group relative bg-[#111] border border-white/[0.06] rounded-xl p-3.5 hover:border-white/10 transition-colors duration-150">
                            <div class="flex items-center gap-3">
                                {{-- Service Icon --}}
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                                     :style="`background:${slot.service_color}18`">
                                    <span class="material-symbols-outlined" style="font-size:18px;"
                                          :style="`color:${slot.service_color}`">schedule</span>
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate" x-text="slot.service_name"></p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        <span x-text="slot.start_time"></span>
                                        <span class="text-gray-700 mx-1">â€“</span>
                                        <span x-text="slot.end_time"></span>
                                    </p>
                                </div>

                                {{-- Stats + Delete --}}
                                <div class="flex items-center gap-2 shrink-0">
                                    <div class="text-right">
                                        <span class="block text-[10px] text-gray-600" x-text="slot.total_slots + ' slots'"></span>
                                        <span class="block text-[10px] text-[#1392EC]" x-text="slot.free_slots + ' free'"></span>
                                    </div>
                                    <button @click="deleteTimeslot(slot)"
                                        :disabled="deleting === slot.id"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-600 hover:text-red-400 hover:bg-red-500/10 opacity-0 group-hover:opacity-100 transition-all duration-150 disabled:opacity-40">
                                        <span class="material-symbols-outlined" style="font-size:15px;"
                                              x-text="deleting === slot.id ? 'hourglass_empty' : 'delete'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Divider --}}
            <div class="mx-5 border-t border-white/5"></div>

            {{-- Add New Timeslot Form --}}
            <div class="px-5 py-5" id="add-timeslot-section">

                {{-- Section Header --}}
                <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3">Add Availability</h4>

                {{-- Mode Toggle --}}
                <div class="flex gap-1 bg-[#111] rounded-xl p-1 mb-4">
                    <button type="button"
                        @click="switchMode('specific')"
                        :class="form.mode === 'specific'
                            ? 'bg-[#1392EC] text-white shadow-md shadow-[#1392EC]/20'
                            : 'text-gray-500 hover:text-gray-300'"
                        class="flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined" style="font-size:13px;">calendar_month</span>
                        Specific Dates
                    </button>
                    <button type="button"
                        @click="switchMode('recurring')"
                        :class="form.mode === 'recurring'
                            ? 'bg-[#1392EC] text-white shadow-md shadow-[#1392EC]/20'
                            : 'text-gray-500 hover:text-gray-300'"
                        class="flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined" style="font-size:13px;">autorenew</span>
                        Recurring
                    </button>
                </div>

                <div class="space-y-3">

                    {{-- â”€â”€ SPECIFIC DATES MODE â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
                    <div x-show="form.mode === 'specific'" x-cloak>

                        {{-- Selected Dates Chips --}}
                        <div class="mb-2">
                            <label class="block text-xs text-gray-500 mb-2">Selected Dates
                                <span class="text-gray-600 ml-1" x-text="`(${form.dates.length})`"></span>
                            </label>

                            {{-- Chip list --}}
                            <div class="flex flex-wrap gap-1.5 mb-2" x-show="form.dates.length > 0">
                                <template x-for="(d, i) in form.dates" :key="d">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-[#1392EC]/15 border border-[#1392EC]/25 text-[#1392EC] text-[11px] font-medium rounded-lg">
                                        <span x-text="formatDateShort(d)"></span>
                                        <button type="button" @click="removeDate(i)"
                                            class="text-[#1392EC]/60 hover:text-[#1392EC] transition-colors ml-0.5">
                                            <span class="material-symbols-outlined" style="font-size:12px;">close</span>
                                        </button>
                                    </span>
                                </template>
                            </div>

                            {{-- Empty state --}}
                            <p x-show="form.dates.length === 0" x-cloak
                               class="text-xs text-gray-600 italic mb-2">No dates selected â€” click the calendar or add below.</p>

                            {{-- Add extra date picker --}}
                            <div class="flex gap-2">
                                <input type="date" x-model="newDateInput"
                                    :min="todayStr"
                                    class="flex-1 bg-[#111] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]/50 focus:border-[#1392EC]/50 transition-colors">
                                <button type="button" @click="addExtraDate()"
                                    class="px-3 py-2 bg-white/5 hover:bg-white/10 text-gray-300 text-xs font-medium rounded-xl transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined" style="font-size:14px;">add</span>
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- â”€â”€ RECURRING MODE â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
                    <div x-show="form.mode === 'recurring'" x-cloak class="space-y-3">

                        {{-- Weekday picker --}}
                        <div>
                            <label class="block text-xs text-gray-500 mb-2">Repeat on</label>
                            <div class="flex gap-1 flex-wrap">
                                @php
                                    $days = [
                                        ['val' => 1, 'label' => 'Mon'],
                                        ['val' => 2, 'label' => 'Tue'],
                                        ['val' => 3, 'label' => 'Wed'],
                                        ['val' => 4, 'label' => 'Thu'],
                                        ['val' => 5, 'label' => 'Fri'],
                                        ['val' => 6, 'label' => 'Sat'],
                                        ['val' => 0, 'label' => 'Sun'],
                                    ];
                                @endphp
                                @foreach($days as $day)
                                <button type="button"
                                    @click="toggleWeekday({{ $day['val'] }})"
                                    :class="form.weekdays.includes({{ $day['val'] }})
                                        ? 'bg-[#1392EC] text-white border-[#1392EC]'
                                        : 'bg-[#111] text-gray-500 border-white/10 hover:border-white/20 hover:text-gray-300'"
                                    class="w-10 h-9 text-[11px] font-semibold rounded-lg border transition-all duration-150">
                                    {{ $day['label'] }}
                                </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Date range --}}
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1.5">Start Date</label>
                                <input type="date" x-model="form.start_date" :min="todayStr"
                                    class="w-full bg-[#111] border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]/50 focus:border-[#1392EC]/50 transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1.5">End Date</label>
                                <input type="date" x-model="form.end_date" :min="form.start_date || todayStr"
                                    class="w-full bg-[#111] border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]/50 focus:border-[#1392EC]/50 transition-colors">
                            </div>
                        </div>

                        {{-- Preview count --}}
                        <p class="text-[11px] text-gray-600 flex items-center gap-1" x-show="recurringPreviewCount > 0" x-cloak>
                            <span class="material-symbols-outlined" style="font-size:13px;">info</span>
                            This will create availability on <strong class="text-gray-400" x-text="recurringPreviewCount"></strong> date(s).
                        </p>
                    </div>

                    {{-- â”€â”€ SHARED FIELDS (both modes) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}

                    {{-- Service --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5">Service</label>
                        <select x-model="form.service_id"
                            class="w-full bg-[#111] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]/50 focus:border-[#1392EC]/50 transition-colors">
                            <option value="">Select service...</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Times --}}
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1.5">Start Time</label>
                            <input type="time" x-model="form.start_time"
                                class="w-full bg-[#111] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]/50 focus:border-[#1392EC]/50 transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1.5">End Time</label>
                            <input type="time" x-model="form.end_time"
                                class="w-full bg-[#111] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]/50 focus:border-[#1392EC]/50 transition-colors">
                        </div>
                    </div>

                    {{-- Duration --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5">Slot Duration</label>
                        <select x-model="form.slot_duration"
                            class="w-full bg-[#111] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]/50 focus:border-[#1392EC]/50 transition-colors">
                            <option value="15">15 minutes</option>
                            <option value="20">20 minutes</option>
                            <option value="30">30 minutes</option>
                        </select>
                    </div>

                    {{-- Error --}}
                    <div x-show="formError" x-cloak
                         class="px-3 py-2.5 bg-red-500/8 border border-red-500/15 rounded-xl flex items-start gap-2">
                        <span class="material-symbols-outlined text-red-400 mt-0.5 shrink-0" style="font-size:14px;">error</span>
                        <p class="text-xs text-red-400" x-text="formError"></p>
                    </div>

                    {{-- Success --}}
                    <div x-show="formSuccess" x-cloak
                         class="px-3 py-2.5 bg-[#1392EC]/8 border border-[#1392EC]/15 rounded-xl flex items-start gap-2">
                        <span class="material-symbols-outlined text-[#1392EC] mt-0.5 shrink-0" style="font-size:14px;">check_circle</span>
                        <p class="text-xs text-[#1392EC]" x-text="formSuccess"></p>
                    </div>

                    {{-- Submit --}}
                    <button @click="addBulk()" :disabled="submitting"
                        class="w-full py-2.5 bg-[#1392EC] hover:bg-[#1392EC]/90 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-xl transition-all duration-150 shadow-md shadow-[#1392EC]/20 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:17px;" x-show="!submitting">add</span>
                        <span class="material-symbols-outlined" style="font-size:17px; animation: spin 1s linear infinite;" x-show="submitting" x-cloak>progress_activity</span>
                        <span x-text="submitting ? 'Saving...' : (form.mode === 'specific' ? 'Add to Selected Dates' : 'Create Recurring Schedule')"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('modals')
{{-- Cancellation Warning Modal (has bookings) --}}
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
                <span class="font-semibold">Warning:</span> Deleting this block will cancel
                <span id="cancel-modal-count" class="font-bold"></span> booked appointment(s).
                Affected students will be notified.
            </p>
        </div>
        <div class="mb-5">
            <label class="block text-xs text-gray-400 mb-1.5">Cancellation Reason <span class="text-red-400">*</span></label>
            <textarea id="cancel-modal-reason" rows="3"
                class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-red-400/50 resize-none"
                placeholder="Explain why this availability is being removed..."></textarea>
            <p id="cancel-modal-error" class="text-xs text-red-400 mt-1 hidden">Please provide a cancellation reason.</p>
        </div>
        <div class="flex gap-3">
            <button type="button" id="cancel-modal-close"
                class="flex-1 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-medium rounded-xl transition-colors">Cancel</button>
            <button type="button" id="cancel-modal-confirm"
                class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">Delete & Cancel Appointments</button>
        </div>
    </div>
</div>

{{-- Simple Delete Modal (no bookings) --}}
<div id="simple-delete-modal" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl w-full max-w-sm mx-4 p-6 shadow-2xl">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-gray-500/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-gray-400" style="font-size:22px;">delete</span>
            </div>
            <h3 class="text-base font-semibold text-white">Delete Availability Block?</h3>
        </div>
        <p class="text-sm text-gray-400 mb-5" id="simple-delete-info">Are you sure you want to delete this availability block and all its time slots?</p>
        <div class="flex gap-3">
            <button type="button" id="simple-delete-close"
                class="flex-1 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-medium rounded-xl transition-colors">Cancel</button>
            <button type="button" id="simple-delete-confirm"
                class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">Delete</button>
        </div>
    </div>
</div>
@endpush

@push('modals')
{{-- Edit Availability Modal (List View) --}}
<div id="edit-modal" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl w-full max-w-md mx-4 p-6 shadow-2xl" @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#1392EC]/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#1392EC]" style="font-size:20px;">edit_calendar</span>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-white">Edit Availability</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5" id="edit-modal-date"></p>
                </div>
            </div>
            <button type="button" id="edit-modal-close"
                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-white hover:bg-white/8 rounded-lg transition-all">
                <span class="material-symbols-outlined" style="font-size:20px;">close</span>
            </button>
        </div>

        {{-- Locked notice (shown when appointments exist) --}}
        <div id="edit-modal-locked-notice"
             class="hidden mb-4 px-3 py-2.5 bg-amber-500/8 border border-amber-500/15 rounded-xl flex items-start gap-2">
            <span class="material-symbols-outlined text-amber-400 mt-0.5 shrink-0" style="font-size:15px;">lock</span>
            <p class="text-xs text-amber-400">This block has booked appointments. Time and duration fields are locked.</p>
        </div>

        <div class="space-y-3">
            {{-- Service --}}
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Service</label>
                <select id="edit-service-id"
                    class="w-full bg-[#141414] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]/50">
                    @foreach($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Times --}}
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">Start Time</label>
                    <input type="time" id="edit-start-time"
                        class="w-full bg-[#141414] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]/50 disabled:opacity-40 disabled:cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5">End Time</label>
                    <input type="time" id="edit-end-time"
                        class="w-full bg-[#141414] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]/50 disabled:opacity-40 disabled:cursor-not-allowed">
                </div>
            </div>

            {{-- Slot Duration --}}
            <div>
                <label class="block text-xs text-gray-500 mb-1.5">Slot Duration</label>
                <select id="edit-slot-duration"
                    class="w-full bg-[#141414] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]/50 disabled:opacity-40 disabled:cursor-not-allowed">
                    <option value="15">15 minutes</option>
                    <option value="20">20 minutes</option>
                    <option value="30">30 minutes</option>
                </select>
            </div>

            {{-- Error --}}
            <p id="edit-modal-error" class="hidden text-xs text-red-400 bg-red-500/8 border border-red-500/15 rounded-xl px-3 py-2"></p>

            {{-- Actions --}}
            <div class="flex gap-2.5 pt-1">
                <button type="button" id="edit-modal-cancel"
                    class="flex-1 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-medium rounded-xl transition-colors">Cancel</button>
                <button type="button" id="edit-modal-save"
                    class="flex-1 py-2.5 bg-[#1392EC] hover:bg-[#1392EC]/90 text-white text-sm font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:16px;">save</span>
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
@endpush


@push('styles')
<style>
    /* â”€â”€ FullCalendar Dark Theme â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .fc-theme-standard .fc-scrollgrid { border-color: rgba(255,255,255,0.05); }
    .fc-theme-standard td, .fc-theme-standard th { border-color: rgba(255,255,255,0.05); }
    .fc .fc-toolbar-title { font-size: 1.05rem; font-weight: 600; color: white; }
    .fc .fc-button-primary {
        background-color: #1392EC; border-color: #1392EC;
        text-transform: capitalize; border-radius: 0.5rem;
        font-size: 0.8rem; padding: 0.25rem 0.65rem;
    }
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active,
    .fc .fc-button-primary:hover { background-color: #0d82d6; border-color: #0d82d6; }
    .fc .fc-button-primary:disabled { background-color: rgba(19,146,236,0.4); border-color: transparent; }
    .fc-day-today { background-color: rgba(19,146,236,0.05) !important; }
    .fc-event { cursor: pointer; border-radius: 4px; padding: 1px 5px; font-size: 0.72rem; border: none; }
    .fc-col-header-cell-cushion { color: #9ca3af; font-weight: 500; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .fc-daygrid-day-number { color: #d1d5db; font-size: 0.82rem; padding: 4px !important; }

    /* Clickable date cells (future and today) */
    .fc-daygrid-day:not(.fc-day-past) { cursor: pointer; }
    .fc-daygrid-day:not(.fc-day-past):hover .fc-daygrid-day-frame { background-color: rgba(19,146,236,0.06); }

    /* Disabled past dates */
    .fc-day-past .fc-daygrid-day-frame,
    .fc-day-past.fc-timegrid-col {
        opacity: 0.4;
        cursor: not-allowed !important;
        background-color: rgba(0, 0, 0, 0.1);
    }

    /* Selected date */
    .fc-day-selected .fc-daygrid-day-frame {
        background-color: rgba(19,146,236,0.12) !important;
        box-shadow: inset 0 0 0 2px rgba(19,146,236,0.35);
    }
    .fc-day-selected .fc-daygrid-day-number { color: #1392EC; font-weight: 700; }

    /* Custom View Switcher styling */
    .view-month .fc-monthView-button,
    .view-week .fc-weekView-button,
    .view-list .fc-listView-button {
        background-color: #0d82d6 !important;
        border-color: #0d82d6 !important;
        box-shadow: inset 0 3px 5px rgba(0,0,0,0.125) !important;
    }
    
    .view-list .fc-view-harness {
        display: none !important;
    }
    
    .view-list.fc {
        height: auto !important;
        min-height: 0 !important;
    }
    
    .view-list .fc-toolbar-chunk:nth-child(1),
    .view-list .fc-toolbar-chunk:nth-child(2) {
        pointer-events: none;
        opacity: 0;
    }

    /* Drawer shadow */
    #availability-drawer {
        box-shadow: -12px 0 40px rgba(0,0,0,0.5);
    }

    /* Spinner animation for progress_activity icon */
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
function availabilityManager() {

    // GPU-only drawer animation â€” direct DOM manipulation bypasses Alpine
    // reactivity diffing, ensuring true 60fps transform-only transitions.
    const getDrawer   = () => document.getElementById('availability-drawer');
    const getBackdrop = () => document.getElementById('drawer-backdrop');

    function openDrawer() {
        const d = getDrawer(), b = getBackdrop();
        if (d) d.style.transform = 'translateX(0)';
        if (b) b.style.background = 'rgba(0,0,0,0.25)';
    }

    function closeDrawer() {
        const d = getDrawer(), b = getBackdrop();
        if (d) d.style.transform = 'translateX(100%)';
        if (b) b.style.background = 'rgba(0,0,0,0)';
    }

    return {
        // â”€â”€ Core state â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        panelOpen:            false,
        selectedDate:         null,
        selectedDateFormatted: '',
        timeslots:            [],
        loading:              false,
        deleting:             null,
        submitting:           false,
        formError:            '',
        formSuccess:          '',
        calendar:             null,
        pendingDeleteSlot:    null,
        newDateInput:         '',
        activeView:           'month',
        listGroups:           [],
        listLoading:          false,
        csrfToken: document.querySelector('meta[name="csrf-token"]').content,

        // â”€â”€ Computed getters â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        get todayStr() {
            const n = new Date();
            return `${n.getFullYear()}-${String(n.getMonth()+1).padStart(2,'0')}-${String(n.getDate()).padStart(2,'0')}`;
        },

        get recurringPreviewCount() {
            if (this.form.mode !== 'recurring') return 0;
            const { start_date, end_date, weekdays } = this.form;
            if (!start_date || !end_date || weekdays.length === 0) return 0;
            const start = new Date(start_date + 'T00:00:00');
            const end   = new Date(end_date   + 'T00:00:00');
            if (start > end) return 0;
            let count = 0, cur = new Date(start);
            while (cur <= end) {
                if (weekdays.includes(cur.getDay())) count++;
                cur.setDate(cur.getDate() + 1);
            }
            return count;
        },

        // â”€â”€ Form state â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        form: {
            mode:         'specific', // 'specific' | 'recurring'
            dates:        [],         // specific mode: array of YYYY-MM-DD strings
            weekdays:     [],         // recurring mode: 0=Sun â€¦ 6=Sat
            start_date:   '',
            end_date:     '',
            service_id:   '',
            start_time:   '',
            end_time:     '',
            slot_duration: '15',
        },

        // â”€â”€ Init â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

        init() {
            this.initCalendar();
            this.initModals();
            this.initEditModal();
        },

        // -- View switching -----------------------------------------------

        setView(view) {
            if (view === this.activeView) return;
            if (view === 'list') {
                if (this.panelOpen) this.closePanel();
                this.activeView = 'list';
                this.fetchList();
                return;
            }
            this.activeView = view;
            this.$nextTick(() => {
                this.calendar?.changeView(view === 'week' ? 'timeGridWeek' : 'dayGridMonth');
            });
        },

        async fetchList() {
            this.listLoading = true;
            this.listGroups  = [];
            try {
                const res = await fetch('{{ route("staff.availability.list") }}', {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                this.listGroups = await res.json();
            } catch (e) {
                console.error('fetchList:', e);
            } finally {
                this.listLoading = false;
            }
        },

        openEditModal(slot) {
            const modal      = document.getElementById('edit-modal');
            const lockedNote = document.getElementById('edit-modal-locked-notice');
            const errEl      = document.getElementById('edit-modal-error');
            const startEl    = document.getElementById('edit-start-time');
            const endEl      = document.getElementById('edit-end-time');
            const durEl      = document.getElementById('edit-slot-duration');

            document.getElementById('edit-modal-date').textContent =
                slot.date_label + ' \u2014 ' + slot.date_formatted;
            document.getElementById('edit-service-id').value = slot.service_id;
            startEl.value = slot.start_time_raw;
            endEl.value   = slot.end_time_raw;
            durEl.value   = String(slot.slot_duration);
            errEl.classList.add('hidden');

            const locked = slot.has_bookings;
            startEl.disabled = locked;
            endEl.disabled   = locked;
            durEl.disabled   = locked;
            locked ? lockedNote.classList.remove('hidden') : lockedNote.classList.add('hidden');

            modal._slot = slot;
            modal.classList.replace('hidden', 'flex');
            document.body.style.overflow = 'hidden';
        },

        initEditModal() {
            const self    = this;
            const modal   = document.getElementById('edit-modal');
            const errEl   = document.getElementById('edit-modal-error');
            const saveBtn = document.getElementById('edit-modal-save');

            const closeEdit = () => {
                modal.classList.replace('flex', 'hidden');
                document.body.style.overflow = '';
            };

            document.getElementById('edit-modal-close').addEventListener('click', closeEdit);
            document.getElementById('edit-modal-cancel').addEventListener('click', closeEdit);
            modal.addEventListener('click', e => { if (e.target === modal) closeEdit(); });

            saveBtn.addEventListener('click', async () => {
                const slot  = modal._slot;
                if (!slot) return;

                const startEl = document.getElementById('edit-start-time');
                const endEl   = document.getElementById('edit-end-time');
                const durEl   = document.getElementById('edit-slot-duration');
                const svcEl   = document.getElementById('edit-service-id');

                if (!slot.has_bookings && startEl.value >= endEl.value) {
                    errEl.textContent = 'End time must be after start time.';
                    errEl.classList.remove('hidden');
                    return;
                }

                errEl.classList.add('hidden');
                saveBtn.disabled = true;

                const payload = { service_id: svcEl.value };
                if (!slot.has_bookings) {
                    payload.start_time    = startEl.value;
                    payload.end_time      = endEl.value;
                    payload.slot_duration = durEl.value;
                }

                try {
                    const res  = await fetch(slot.update_url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': self.csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(payload),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        errEl.textContent = data.errors
                            ? Object.values(data.errors).flat().join(' ')
                            : (data.message || 'Save failed.');
                        errEl.classList.remove('hidden');
                        return;
                    }
                    closeEdit();
                    self.fetchList();
                    self.refetchCalendar();
                } catch {
                    errEl.textContent = 'Network error. Please try again.';
                    errEl.classList.remove('hidden');
                } finally {
                    saveBtn.disabled = false;
                }
            });
        },

        // â”€â”€ Calendar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

        initCalendar() {
            const self = this;
            const calendarEl    = document.getElementById('calendar');
            const serviceFilter = document.getElementById('calendar-service-filter');

            this.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 560,
                customButtons: {
                    monthView: { text: 'Month', click: function() { self.setView('month'); } },
                    weekView: { text: 'Week', click: function() { self.setView('week'); } },
                    listView: { text: 'List', click: function() { self.setView('list'); } }
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'monthView,weekView,listView',
                },
                themeSystem: 'standard',
                dateClick(info) { 
                    if (info.dateStr.substring(0, 10) < self.todayStr) return;
                    self.selectDate(info.dateStr.substring(0, 10)); 
                },
                events(info, success, failure) {
                    const serviceId = serviceFilter.value;
                    const url = new URL('{{ route("staff.availability.calendar") }}', window.location.origin);
                    if (serviceId) url.searchParams.append('service_id', serviceId);

                    fetch(url)
                        .then(r => r.json())
                        .then(data => success(data.map(slot => ({
                            id:              slot.id,
                            title:           `${slot.service_name} (${slot.available_slots} free)`,
                            start:           `${slot.date}T${slot.start_time}`,
                            end:             `${slot.date}T${slot.end_time}`,
                            backgroundColor: slot.service_color || '#1392EC',
                            borderColor:     slot.service_color || '#1392EC',
                        }))))
                        .catch(failure);
                },
            });

            this.calendar.render();
        },

        refetchCalendar() { this.calendar?.refetchEvents(); },

        // â”€â”€ Date selection â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

        selectDate(dateStr) {
            // Highlight cell
            document.querySelectorAll('.fc-day-selected').forEach(el => el.classList.remove('fc-day-selected'));
            document.querySelector(`[data-date="${dateStr}"]`)?.classList.add('fc-day-selected');

            this.selectedDate          = dateStr;
            this.selectedDateFormatted = this.formatDate(dateStr);
            this.formError             = '';
            this.formSuccess           = '';

            // Auto-add to specific dates list (deduped)
            if (!this.form.dates.includes(dateStr)) {
                this.form.dates = [dateStr, ...this.form.dates];
            }

            this.panelOpen = true;
            openDrawer();
            this.fetchTimeslots();
        },

        closePanel() {
            this.panelOpen = false;
            closeDrawer();
            document.querySelectorAll('.fc-day-selected').forEach(el => el.classList.remove('fc-day-selected'));
            this.selectedDate = null;
        },

        quickAdd() {
            const today = this.todayStr;
            if (this.selectedDate === today && this.panelOpen) { this._scrollToForm(); return; }
            this.calendar?.today();
            this.selectDate(today);
            setTimeout(() => this._scrollToForm(), 300);
        },

        _scrollToForm() {
            const section    = document.getElementById('add-timeslot-section');
            const scrollArea = document.getElementById('panel-scroll-area');
            if (section && scrollArea) scrollArea.scrollTo({ top: section.offsetTop, behavior: 'smooth' });
        },

        // â”€â”€ Mode & form helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

        switchMode(mode) {
            this.form.mode   = mode;
            this.formError   = '';
            this.formSuccess = '';
        },

        addExtraDate() {
            const d = this.newDateInput;
            if (!d || this.form.dates.includes(d)) { this.newDateInput = ''; return; }
            this.form.dates.push(d);
            this.form.dates.sort();
            this.newDateInput = '';
        },

        removeDate(index) { this.form.dates.splice(index, 1); },

        toggleWeekday(day) {
            const idx = this.form.weekdays.indexOf(day);
            idx === -1 ? this.form.weekdays.push(day) : this.form.weekdays.splice(idx, 1);
        },

        // â”€â”€ Fetch timeslots for selected date â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

        async fetchTimeslots() {
            this.loading = true;
            this.timeslots = [];
            try {
                const res = await fetch(
                    `{{ route('staff.availability.by-date') }}?date=${this.selectedDate}`,
                    { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }
                );
                this.timeslots = await res.json();
            } catch (e) {
                console.error('fetchTimeslots:', e);
            } finally {
                this.loading = false;
            }
        },

        // â”€â”€ Add availability (bulk / recurring) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

        async addBulk() {
            this.formError   = '';
            this.formSuccess = '';

            if (!this.form.service_id)                     return this.formError = 'Please select a service.';
            if (!this.form.start_time)                     return this.formError = 'Please set a start time.';
            if (!this.form.end_time)                       return this.formError = 'Please set an end time.';
            if (this.form.start_time >= this.form.end_time) return this.formError = 'End time must be after start time.';

            if (this.form.mode === 'specific') {
                if (this.form.dates.length === 0) return this.formError = 'Please select at least one date.';
            } else {
                if (this.form.weekdays.length === 0) return this.formError = 'Please select at least one weekday.';
                if (!this.form.start_date)           return this.formError = 'Please set a start date.';
                if (!this.form.end_date)             return this.formError = 'Please set an end date.';
                if (this.form.start_date > this.form.end_date) return this.formError = 'End date must be after start date.';
            }

            this.submitting = true;

            try {
                const payload = {
                    mode:          this.form.mode,
                    service_id:    this.form.service_id,
                    start_time:    this.form.start_time,
                    end_time:      this.form.end_time,
                    slot_duration: this.form.slot_duration,
                };
                if (this.form.mode === 'specific') {
                    payload.dates = this.form.dates;
                } else {
                    payload.weekdays   = this.form.weekdays;
                    payload.start_date = this.form.start_date;
                    payload.end_date   = this.form.end_date;
                }

                const res  = await fetch('{{ route("staff.availability.store-bulk") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (!res.ok) {
                    this.formError = data.errors
                        ? Object.values(data.errors).flat().join(' ')
                        : (data.message || 'Failed to create availability.');
                    return;
                }

                this.formSuccess     = data.message || 'Availability created!';
                this.form.dates      = [];
                this.form.weekdays   = [];
                this.form.start_date = '';
                this.form.end_date   = '';

                if (this.activeView === 'list') {
                    this.fetchList();
                } else if (this.selectedDate) {
                    this.fetchTimeslots();
                }
                this.refetchCalendar();
                setTimeout(() => this.formSuccess = '', 5000);

            } catch {
                this.formError = 'Network error. Please try again.';
            } finally {
                this.submitting = false;
            }
        },

        // â”€â”€ Delete â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

        async deleteTimeslot(slot) {
            this.pendingDeleteSlot = slot;
            try {
                const res  = await fetch(slot.check_url, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                data.has_bookings ? this.showCancelModal(slot, data.booked_count) : this.showSimpleDeleteModal(slot);
            } catch {
                this.showSimpleDeleteModal(slot);
            }
        },

        async confirmDelete(slot, cancellationReason = null) {
            this.deleting = slot.id;
            try {
                const body = { _method: 'DELETE' };
                if (cancellationReason) body.cancellation_reason = cancellationReason;
                const res = await fetch(slot.destroy_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(body),
                });
                if (res.ok) {
                    if (this.activeView === 'list') {
                        this.fetchList();
                    } else {
                        this.fetchTimeslots();
                    }
                    this.refetchCalendar();
                }
            } catch (e) {
                console.error('confirmDelete:', e);
            } finally {
                this.deleting = null;
            }
        },

        // â”€â”€ Modals â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

        showCancelModal(slot, bookedCount) {
            document.getElementById('cancel-modal-info').textContent  = `${slot.service_name} â€” ${this.selectedDateFormatted}`;
            document.getElementById('cancel-modal-count').textContent = bookedCount;
            document.getElementById('cancel-modal-reason').value      = '';
            document.getElementById('cancel-modal-error').classList.add('hidden');
            document.getElementById('cancel-modal').classList.replace('hidden', 'flex');
            document.body.style.overflow = 'hidden';
        },

        showSimpleDeleteModal(slot) {
            document.getElementById('simple-delete-info').textContent =
                `Delete "${slot.service_name}" (${slot.start_time} â€“ ${slot.end_time}) and all its time slots?`;
            document.getElementById('simple-delete-modal').classList.replace('hidden', 'flex');
            document.body.style.overflow = 'hidden';
        },

        closeModalEl(modal) {
            modal.classList.replace('flex', 'hidden');
            document.body.style.overflow = '';
        },

        initModals() {
            const self = this;

            const cancelModal  = document.getElementById('cancel-modal');
            const cancelReason = document.getElementById('cancel-modal-reason');
            const cancelError  = document.getElementById('cancel-modal-error');

            document.getElementById('cancel-modal-close').addEventListener('click', () => self.closeModalEl(cancelModal));
            cancelModal.addEventListener('click', e => { if (e.target === cancelModal) self.closeModalEl(cancelModal); });
            document.getElementById('cancel-modal-confirm').addEventListener('click', () => {
                if (!cancelReason.value.trim()) { cancelError.classList.remove('hidden'); cancelReason.focus(); return; }
                self.closeModalEl(cancelModal);
                if (self.pendingDeleteSlot) self.confirmDelete(self.pendingDeleteSlot, cancelReason.value.trim());
            });
            cancelReason.addEventListener('input', () => cancelError.classList.add('hidden'));

            const simpleModal = document.getElementById('simple-delete-modal');
            document.getElementById('simple-delete-close').addEventListener('click', () => self.closeModalEl(simpleModal));
            simpleModal.addEventListener('click', e => { if (e.target === simpleModal) self.closeModalEl(simpleModal); });
            document.getElementById('simple-delete-confirm').addEventListener('click', () => {
                self.closeModalEl(simpleModal);
                if (self.pendingDeleteSlot) self.confirmDelete(self.pendingDeleteSlot);
            });
        },

        // â”€â”€ Utilities â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

        formatDate(dateStr) {
            const d = new Date(dateStr + 'T00:00:00');
            return d.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        },

        formatDateShort(dateStr) {
            const d = new Date(dateStr + 'T00:00:00');
            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        },
    };
}
</script>
@endpush
