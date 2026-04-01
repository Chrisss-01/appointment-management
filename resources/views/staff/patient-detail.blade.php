@extends('layouts.app')
@section('title', 'Patient Details - ' . $patient->name)
@section('page-title', 'Patient Details')
@section('sidebar') @include('partials.sidebar-staff') @endsection

@section('content')
{{-- Patient Info --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('staff.patients') }}" class="text-gray-400 hover:text-white transition-colors">
        <span class="material-symbols-outlined" style="font-size:20px;">arrow_back</span>
    </a>
    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#1392EC]/30 to-[#1392EC]/50 border border-[#1392EC]/20 flex items-center justify-center text-white text-lg font-bold">
        {{ strtoupper(substr($patient->name, 0, 1)) }}
    </div>
    <div>
        <h2 class="text-lg font-bold text-white">{{ $patient->name }}</h2>
        <p class="text-xs text-gray-500">{{ $patient->student_id ?? '' }} · {{ $patient->program ?? '' }} · {{ $patient->email }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Medical Records --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden flex flex-col h-full">
        <div class="px-5 py-4 border-b border-white/5">
            <h3 class="text-sm font-semibold text-white">Medical Records</h3>
        </div>
        @if($medicalRecords->isEmpty())
        <div class="px-5 py-8 text-center text-gray-500 text-sm">No records yet</div>
        @else
        <div class="divide-y divide-white/5 flex-1 overflow-y-auto custom-scrollbar">
            @foreach($medicalRecords as $record)
            @php
                $serviceColor = $record->appointment?->service?->color;
                $fallbackColor = match($record->record_type) {
                    'consultation' => '#1392EC',
                    'dental' => '#3B82F6',
                    default => '#F59E0B'
                };
                $finalColor = $serviceColor ?? $fallbackColor;
            @endphp
            <div class="px-5 py-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium" style="color: {{ $finalColor }};">{{ $record->service_name ?? (ucfirst($record->record_type) . ' Record') }}</span>
                    <span class="text-[10px] text-gray-600">{{ $record->created_at->format('M d, Y') }}</span>
                </div>
                @if($record->chief_complaint) <p class="text-sm text-gray-300"><span class="text-gray-500">Complaint:</span> {{ $record->chief_complaint }}</p> @endif
                @if($record->diagnosis) <p class="text-sm text-gray-300 mt-1"><span class="text-gray-500">Diagnosis:</span> {{ $record->diagnosis }}</p> @endif
                @if($record->treatment) <p class="text-sm text-gray-300 mt-1"><span class="text-gray-500">Treatment:</span> {{ $record->treatment }}</p> @endif
                @if($record->prescription) <p class="text-sm text-gray-300 mt-1"><span class="text-gray-500">Prescription:</span> {{ $record->prescription }}</p> @endif
                
                @if($record->vital_signs)
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($record->vital_signs as $key => $val)
                    @if($val)
                    <span class="px-2 py-1 bg-white/5 rounded text-[10px] text-gray-400">
                        <span class="text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> {{ $val }}
                    </span>
                    @endif
                    @endforeach
                </div>
                @endif

                @if($record->visual_acuity)
                @php
                    $va = $record->visual_acuity;
                    $vaLabels = [
                        'wears_correction' => 'Wears Correction',
                        'od'               => 'OD',
                        'os'               => 'OS',
                        'color_vision'     => 'Color Vision',
                        'recommendation'   => 'Recommendation',
                    ];
                    $vaFormats = [
                        'wears_correction' => fn($v) => ucfirst($v),
                        'color_vision'     => fn($v) => ucfirst($v),
                        'recommendation'   => fn($v) => ucwords(str_replace('_', ' ', $v)),
                    ];
                @endphp
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($vaLabels as $key => $label)
                    @if(!empty($va[$key]))
                    <span class="px-2 py-1 bg-white/5 rounded text-[10px] text-gray-400">
                        <span class="text-gray-500">{{ $label }}:</span>
                        {{ isset($vaFormats[$key]) ? $vaFormats[$key]($va[$key]) : $va[$key] }}
                    </span>
                    @endif
                    @endforeach
                </div>
                @endif

                @if($record->notes)
                <p class="text-sm text-gray-300 mt-1"><span class="text-gray-500">Notes:</span> {{ $record->notes }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Add Medical Record --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-4">Add Medical Record</h3>
        <form action="{{ route('staff.patients.medical-record') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="student_id" value="{{ $patient->id }}">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Record Type</label>
                    <select name="record_type" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        <option value="consultation">Consultation</option>
                        <option value="dental">Dental</option>
                        <option value="general">General</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Service Name (Title)</label>
                    <input type="text" name="service_name" placeholder="E.g., Vision Screening" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1.5">Chief Complaint</label>
                <textarea name="chief_complaint" rows="2" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none" placeholder="Patient's main concern..."></textarea>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1.5">Diagnosis</label>
                <textarea name="diagnosis" rows="2" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none"></textarea>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1.5">Treatment</label>
                <textarea name="treatment" rows="2" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none"></textarea>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1.5">Prescription</label>
                <textarea name="prescription" rows="2" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none"></textarea>
            </div>

            {{-- Vital Signs --}}
            <div>
                <label class="block text-xs text-gray-400 mb-2">Vital Signs</label>
                <div class="grid grid-cols-2 gap-3">
                    <input type="text" name="vital_signs[blood_pressure]" placeholder="Blood Pressure" class="bg-[#141414] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                    <input type="text" name="vital_signs[temperature]" placeholder="Temperature °C" class="bg-[#141414] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                    <input type="text" name="vital_signs[heart_rate]" placeholder="Heart Rate bpm" class="bg-[#141414] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                    <input type="text" name="vital_signs[weight]" placeholder="Weight kg" class="bg-[#141414] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-[#1392EC] hover:opacity-90 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-[#1392EC]/20">
                Save Medical Record
            </button>
        </form>
    </div>
</div>
@endsection
