@extends('layouts.app')

@section('title', 'My Health Records')
@section('page-title', 'My Health')
@section('sidebar')
    @include('partials.sidebar-student')
@endsection

@section('content')
<div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-white/5">
        <h3 class="text-sm font-semibold text-white">Medical Records</h3>
        <p class="text-xs text-gray-500 mt-0.5">Your complete health history</p>
    </div>

    @if($records->isEmpty())
    <div class="px-5 py-12 text-center">
        <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">medical_information</span>
        <p class="text-gray-400 text-sm">No medical records yet</p>
    </div>
    @else
    <div class="divide-y divide-white/5">
        @foreach($records as $record)
        <div class="px-5 py-4 hover:bg-white/[0.02] transition-colors">
            @php
                $serviceColor = $record->appointment?->service?->color;
                $fallbackColor = match($record->record_type) {
                    'consultation' => '#1392EC',
                    'dental' => '#3B82F6',
                    default => '#F59E0B'
                };
                $finalColor = $serviceColor ?? $fallbackColor;
            @endphp
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: {{ $finalColor }}1a;">
                        <span class="material-symbols-outlined" style="font-size:20px; color: {{ $finalColor }};">
                            {{ $record->record_type === 'dental' ? 'dentistry' : 'clinical_notes' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white">{{ $record->service_name ?? (ucfirst($record->record_type) . ' Record') }}</p>
                        <p class="text-xs text-gray-500">{{ $record->created_at->format('M d, Y · g:i A') }} · Dr. {{ optional($record->staff)->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-3 ml-13 grid grid-cols-1 md:grid-cols-2 gap-3">
                @if($record->chief_complaint)
                <div class="bg-[#141414] rounded-xl p-3">
                    <span class="text-[10px] text-gray-500 uppercase font-medium">Complaint</span>
                    <p class="text-sm text-gray-300 mt-0.5">{{ $record->chief_complaint }}</p>
                </div>
                @endif
                @if($record->diagnosis)
                <div class="bg-[#141414] rounded-xl p-3">
                    <span class="text-[10px] text-gray-500 uppercase font-medium">Diagnosis</span>
                    <p class="text-sm text-gray-300 mt-0.5">{{ $record->diagnosis }}</p>
                </div>
                @endif
                @if($record->treatment)
                <div class="bg-[#141414] rounded-xl p-3">
                    <span class="text-[10px] text-gray-500 uppercase font-medium">Treatment</span>
                    <p class="text-sm text-gray-300 mt-0.5">{{ $record->treatment }}</p>
                </div>
                @endif
                @if($record->prescription)
                <div class="bg-[#141414] rounded-xl p-3">
                    <span class="text-[10px] text-gray-500 uppercase font-medium">Prescription</span>
                    <p class="text-sm text-gray-300 mt-0.5">{{ $record->prescription }}</p>
                </div>
                @endif
            </div>

            @if($record->vital_signs)
            <div class="mt-3 ml-13 flex flex-wrap gap-3">
                @foreach($record->vital_signs as $key => $val)
                @if($val)
                <span class="px-2.5 py-1 bg-white/5 rounded-lg text-xs text-gray-400">
                    <span class="text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> {{ $val }}
                </span>
                @endif
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
    <div class="px-5 py-3 border-t border-white/5">
        {{ $records->links() }}
    </div>
    @endif
</div>
@endsection
