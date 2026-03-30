@extends('layouts.app')
@section('title', 'Record Visits & Consultations')
@section('page-title', 'Record Visits')
@section('sidebar') @include('partials.sidebar-staff') @endsection

@section('content')
<div x-data="consultationFlow()" @keydown.escape.window="closePanel()" class="flex flex-col h-[calc(100vh-8rem)]">

    {{-- Tabs --}}
    <div class="flex gap-2 mb-6 shrink-0">
        <a href="?filter=today" class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ $filter === 'today' ? 'bg-[#1392EC] text-white' : 'bg-[#1A1A1A] text-gray-400 border border-white/5 hover:text-white' }}">
            Today
        </a>
        <a href="?filter=upcoming" class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ $filter === 'upcoming' ? 'bg-[#1392EC] text-white' : 'bg-[#1A1A1A] text-gray-400 border border-white/5 hover:text-white' }}">
            Upcoming
        </a>
    </div>

    {{-- Appointment List --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl flex-1 overflow-hidden flex flex-col relative">
        <div class="px-5 py-4 border-b border-white/5 shrink-0 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-white">{{ $filter === 'today' ? "Today's Queue" : "Upcoming Scheduled" }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">Click an appointment to begin consultation</p>
            </div>
            <div class="text-xs text-gray-400">
                {{ $appointments->count() }} appointment(s)
            </div>
        </div>

        <div class="flex-1 overflow-y-auto">
            @if($appointments->isEmpty())
            <div class="px-5 py-24 text-center">
                <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">event_available</span>
                <p class="text-gray-400 text-sm">No {{ $filter }} appointments to record</p>
            </div>
            @else
            <div class="divide-y divide-white/5">
                @foreach($appointments as $apt)
                <div @click="openPanel({{ $apt->id }})" class="px-5 py-4 flex items-center gap-4 hover:bg-white/[0.03] transition-colors cursor-pointer group">
                    <div class="text-center shrink-0 w-12">
                        <span class="text-lg font-bold text-gray-400 group-hover:text-white transition-colors">
                            #{{ str_pad($apt->queue_number ?? 0, 2, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 border border-white/10 text-white font-bold" 
                         style="background: linear-gradient(135deg, {{ $apt->service->color ?? '#1392EC' }}30, {{ $apt->service->color ?? '#1392EC' }}10);">
                        {{ strtoupper(substr($apt->student->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0 flex flex-col justify-center">
                        <div class="flex items-center gap-2 mb-0.5">
                            <p class="text-sm font-medium text-white truncate">{{ $apt->student->name }}</p>
                            @if($apt->student->student_id)
                                <span class="px-1.5 py-0.5 roundedbg-white/5 text-[9px] text-gray-400 font-mono tracking-wider border border-white/10 uppercase">{{ $apt->student->student_id }}</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-gray-500 flex items-center gap-1.5 truncate">
                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $apt->service->color ?? '#1392EC' }}"></span>
                            {{ $apt->service->name }} · 
                            @if($filter === 'upcoming' && !$apt->date->isToday())
                                {{ $apt->date->format('M d') }} ·
                            @endif
                            {{ \Carbon\Carbon::parse($apt->start_time)->format('g:i A') }}
                        </p>
                        @if($apt->reason)
                            <p class="text-[11px] text-gray-400 mt-1 italic truncate">"{{ $apt->reason }}"</p>
                        @endif
                    </div>
                    <div class="shrink-0 text-gray-600 group-hover:text-[#1392EC] transition-colors flex items-center pr-2">
                        <span class="material-symbols-outlined" style="font-size: 20px;">open_in_new</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Slide-Over Panel --}}
        <div x-show="isOpen" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 z-10 flex">
             
            {{-- Dark Overlay --}}
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closePanel()"></div>
            
            {{-- Spacer for left side --}}
            <div class="flex-1" @click="closePanel()"></div>

            {{-- Panel Content --}}
            <div class="relative w-full max-w-lg bg-[#111111] border-l border-white/10 shadow-2xl flex flex-col h-full overflow-hidden"
                 x-show="isOpen"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 @click.stop>
                 
                {{-- Header --}}
                <div class="px-5 py-4 border-b border-white/5 flex items-center justify-between bg-[#141414] shrink-0">
                    <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#1392EC]" style="font-size: 18px;">assignment_ind</span>
                        Consultation
                    </h3>
                    <button @click="closePanel()" class="text-gray-500 hover:text-white transition-colors rounded-lg p-1 hover:bg-white/5">
                        <span class="material-symbols-outlined" style="font-size: 20px;">close</span>
                    </button>
                </div>

                {{-- Loading State --}}
                <div x-show="isLoading" class="flex-1 flex flex-col items-center justify-center">
                    <span class="material-symbols-outlined animate-spin text-[#1392EC] mb-3" style="font-size: 32px;">progress_activity</span>
                    <p class="text-sm text-gray-400">Loading patient data...</p>
                </div>

                {{-- Actual Content --}}
                <div x-show="!isLoading" class="flex-1 overflow-y-auto custom-scrollbar">
                    
                    {{-- Patient Context Card --}}
                    <div class="p-5 border-b border-white/5 relative overflow-hidden bg-gradient-to-br from-white/[0.02] to-transparent">
                        <div class="flex items-start gap-4 mb-3">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 border border-[#1392EC]/20 text-white font-bold text-lg bg-[#1392EC]/10" x-text="appointment?.student?.name?.charAt(0) || '?'"></div>
                            <div>
                                <h3 class="text-base font-semibold text-white" x-text="appointment?.student?.name"></h3>
                                <p class="text-xs text-gray-400 mt-0.5" x-text="`${appointment?.student?.student_id || ''} · ${appointment?.student?.program || ''} · ${appointment?.student?.year_level || ''} Year`"></p>
                            </div>
                        </div>
                        
                        <div class="bg-[#141414] rounded-xl p-3 border border-white/5 mt-2">
                            <p class="text-xs text-gray-300 font-medium mb-1 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full" :style="`background-color: ${appointment?.service?.color || '#1392EC'}`"></span>
                                <span x-text="appointment?.service?.name"></span>
                                <span class="text-gray-600 px-1">·</span>
                                <span x-text="appointment ? new Date(appointment.date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'}) : ''"></span>
                            </p>
                            <p class="text-[11px] text-gray-500 italic" x-text="appointment?.reason ? `Reason: &quot;${appointment.reason}&quot;` : 'No specific reason provided'"></p>
                        </div>
                    </div>

                    {{-- Collapsible History --}}
                    <div class="border-b border-white/5" x-data="{ expandedHistory: false }">
                        <button @click="expandedHistory = !expandedHistory" class="w-full px-5 py-3 flex items-center justify-between bg-white/[0.01] hover:bg-white/[0.03] transition-colors">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500" style="font-size:16px;">history</span>
                                <span class="text-xs font-semibold text-gray-300" x-text="`Patient History (${history.length} records)`"></span>
                            </div>
                            <span class="material-symbols-outlined text-gray-500 transition-transform" :class="expandedHistory ? 'rotate-180' : ''" style="font-size:16px;">expand_more</span>
                        </button>
                        
                        <div x-show="expandedHistory" x-collapse>
                            <div class="px-5 py-3 space-y-3 bg-[#111] max-h-48 overflow-y-auto custom-scrollbar">
                                <template x-if="history.length === 0">
                                    <p class="text-[11px] text-gray-500 text-center py-2">No past records found.</p>
                                </template>
                                <template x-for="record in history" :key="record.id">
                                    <div class="flex gap-3">
                                        <div class="mt-1 flex flex-col items-center">
                                            <div class="w-1.5 h-1.5 rounded-full bg-gray-500"></div>
                                            <div class="w-px h-full bg-gray-800 my-1"></div>
                                        </div>
                                        <div class="flex-1 pb-1">
                                            <p class="text-[11px] text-gray-400 font-mono tracking-wide" x-text="new Date(record.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year:'numeric'})"></p>
                                            <p class="text-xs text-gray-300 mt-0.5">
                                                <span class="font-medium text-[#1392EC]" x-text="(record.appointment?.service?.name || record.record_type).replace('_', ' ')"></span>: 
                                                <span x-text="record.chief_complaint || 'Routine checkup'"></span>
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Form Area --}}
                    <form id="consultationForm" :action="`/staff/record-visits/${appointment?.id}/consultation`" method="POST" class="p-5 space-y-5">
                        @csrf

                        {{-- Vital Signs (Always shown) --}}
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Vital Signs</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1">Blood Pressure</label>
                                    <input type="text" name="vital_signs[blood_pressure]" placeholder="e.g. 120/80" class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-[#1392EC] transition-colors">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1">Temperature (°C)</label>
                                    <input type="number" step="0.1" name="vital_signs[temperature]" placeholder="e.g. 36.5" class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-[#1392EC] transition-colors">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1">Heart Rate (bpm)</label>
                                    <input type="number" name="vital_signs[heart_rate]" placeholder="e.g. 80" class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-[#1392EC] transition-colors">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1">Weight (kg)</label>
                                    <input type="number" step="0.1" name="vital_signs[weight]" placeholder="e.g. 65" class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-[#1392EC] transition-colors">
                                </div>
                            </div>
                        </div>

                        {{-- Consultation Fields --}}
                        <div x-show="formType === 'standard_consultation' || formType === 'eye_checkup'" class="space-y-4 pt-2 border-t border-white/5">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Consultation Details</h4>
                            
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Chief Complaint</label>
                                <textarea name="chief_complaint" rows="2" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#1392EC] resize-none" placeholder="What is the main problem?"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Diagnosis</label>
                                <textarea name="diagnosis" rows="2" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#1392EC] resize-none"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Treatment Provided</label>
                                <textarea name="treatment" rows="2" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#1392EC] resize-none"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-400 mb-1.5">Prescription / Meds Given</label>
                                <textarea name="prescription" rows="2" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#1392EC] resize-none"></textarea>
                            </div>
                        </div>

                        {{-- Eye Checkup Specifics --}}
                        <div x-show="formType === 'eye_checkup'" class="space-y-3 pt-2 border-t border-white/5">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Visual Acuity</h4>
                            <div class="grid grid-cols-3 gap-3 text-center border bg-[#141414] border-white/5 p-3 rounded-xl">
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1">Left Eye (OD)</label>
                                    <input type="text" name="visual_acuity[left]" placeholder="20/20" class="w-full bg-[#1A1A1A] border border-white/10 text-center rounded text-sm text-white py-1">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1">Right Eye (OS)</label>
                                    <input type="text" name="visual_acuity[right]" placeholder="20/20" class="w-full bg-[#1A1A1A] border border-white/10 text-center rounded text-sm text-white py-1">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-500 mb-1">Both Eyes (OU)</label>
                                    <input type="text" name="visual_acuity[both]" placeholder="20/20" class="w-full bg-[#1A1A1A] border border-white/10 text-center rounded text-sm text-white py-1">
                                </div>
                            </div>
                        </div>

                        {{-- General Notes --}}
                        <div class="pt-2 border-t border-white/5">
                            <label class="block text-xs text-gray-400 mb-1.5">Additional Notes (Private)</label>
                            <textarea name="notes" rows="2" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#1392EC] resize-none" placeholder="Staff notes..."></textarea>
                        </div>
                    </form>
                </div>

                {{-- Footer Actions --}}
                <div class="px-5 py-4 border-t border-white/5 bg-[#141414] shrink-0 flex gap-3">
                    <button type="submit" form="consultationForm" class="flex-1 py-2.5 bg-[#1392EC] hover:bg-[#1392EC]/90 text-white text-sm font-semibold rounded-xl tracking-wide transition-all shadow-lg shadow-[#1392EC]/20 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:18px;">save</span>
                        Save & Complete
                    </button>
                    <form :action="`/staff/appointments/${appointment?.id}/no-show`" method="POST" onsubmit="return confirm('Mark as No Show?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-5 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 text-sm font-medium rounded-xl transition-all h-full">
                            No Show
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
</style>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('consultationFlow', () => ({
            isOpen: false,
            isLoading: false,
            appointment: null,
            history: [],
            formType: 'standard_consultation',
            
            openPanel(id) {
                this.isLoading = true;
                this.isOpen = true;
                
                fetch(`/staff/record-visits/${id}/consultation`)
                    .then(res => res.json())
                    .then(data => {
                        this.appointment = data.appointment;
                        this.history = data.history || [];
                        this.formType = data.appointment?.service?.form_type || 'standard_consultation';
                        this.isLoading = false;
                    })
                    .catch(err => {
                        console.error("Error fetching consultation data", err);
                        this.isLoading = false;
                    });
            },
            
            closePanel() {
                if (!this.isOpen) return;
                this.isOpen = false;
                setTimeout(() => {
                    this.appointment = null;
                    this.history = [];
                }, 300);
            }
        }));
    });
</script>
@endpush
@endsection
