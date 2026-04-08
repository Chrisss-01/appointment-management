@extends('layouts.app')
@section('title', 'Services')
@section('page-title', 'Service Management')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')

@php
$iconOptions = [
    'medical_services' => 'Medical Cross',
    'stethoscope' => 'Stethoscope',
    'dentistry' => 'Tooth / Dental',
    'visibility' => 'Eye / Vision',
    'monitor_heart' => 'Heart Monitor',
    'favorite' => 'Heart',
    'vital_signs' => 'Vital Signs',
    'prescriptions' => 'Prescription',
    'vaccines' => 'Vaccine / Syringe',
    'psychology' => 'Brain / Psychology',
    'healing' => 'Healing',
    'emergency' => 'Emergency',
    'science' => 'Lab / Science',
    'medication' => 'Pills / Medication',
    'description' => 'Document / Form',
    'calendar_month' => 'Calendar / Schedule',
];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Add Service --}}
    <div class="lg:col-span-1">
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Add Service</h3>
            <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Name</label>
                    <input type="text" name="name" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]" placeholder="e.g. Eye Checkup">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Description</label>
                    <textarea name="description" rows="2" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Form Type</label>
                    <select name="form_type" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        <option value="standard_consultation">Standard Consultation</option>
                        <option value="vital_signs_only">Vital Signs Only</option>
                        <option value="vision_screening">Vision Screening</option>
                    </select>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">Duration</label>
                        <input type="number" name="duration_minutes" value="15" min="5" max="60" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">Color</label>
                        <input type="color" name="color" value="#1392EC" class="w-full h-11 bg-[#141414] border border-white/10 rounded-xl px-2 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">Icon</label>
                        <select name="icon" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-3 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                            @foreach($iconOptions as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full py-3 bg-[#1392EC] hover:bg-[#1392EC]/80 text-white text-sm font-semibold rounded-xl transition-all">Add Service</button>
            </form>
        </div>
    </div>

    {{-- Service List --}}
    <div class="lg:col-span-2 space-y-4">
        @foreach($services as $service)
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden" x-data="{ open: false }">
            {{-- Service Header --}}
            <div class="px-5 py-4 flex items-center gap-4 cursor-pointer" @click="open = !open">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $service->color }}15;">
                    <span class="material-symbols-outlined" style="font-size:20px; color: {{ $service->color }};">{{ $service->icon }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white">{{ $service->name }}</p>
                    <p class="text-xs text-gray-500">{{ $service->duration_minutes }} min · {{ $service->appointments_count }} appointments · {{ $service->reasonPresets->count() }} reason presets</p>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $service->is_active ? 'bg-[#1392EC]/10 text-[#1392EC]' : 'bg-red-500/10 text-red-400' }}">
                    {{ $service->is_active ? 'Active' : 'Inactive' }}
                </span>
                <span class="material-symbols-outlined text-gray-500 transition-transform" :class="open && 'rotate-180'" style="font-size:18px;">expand_more</span>
            </div>

            {{-- Expandable Detail --}}
            <div x-show="open" x-cloak class="border-t border-white/5">
                {{-- Edit Service --}}
                <div class="px-5 py-4 border-b border-white/5">
                    <form action="{{ route('admin.services.update', $service) }}" method="POST" class="flex flex-wrap gap-3 items-end">
                        @csrf @method('PUT')
                        <div class="flex-1 min-w-[120px]">
                            <label class="block text-xs text-gray-500 mb-1">Name</label>
                            <input type="text" name="name" value="{{ $service->name }}" required class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        </div>
                        <div class="flex-1 min-w-[120px]">
                            <label class="block text-xs text-gray-500 mb-1">Description</label>
                            <input type="text" name="description" value="{{ $service->description }}" class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        </div>
                        <div class="w-24">
                            <label class="block text-xs text-gray-500 mb-1">Duration</label>
                            <input type="number" name="duration_minutes" value="{{ $service->duration_minutes }}" min="5" max="60" required class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        </div>
                        <div class="flex-1 min-w-[120px]">
                            <label class="block text-xs text-gray-500 mb-1">Form Type</label>
                            <select name="form_type" required class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                                <option value="standard_consultation" {{ $service->form_type === 'standard_consultation' ? 'selected' : '' }}>Consultation</option>
                                <option value="vital_signs_only" {{ $service->form_type === 'vital_signs_only' ? 'selected' : '' }}>Vital Signs Only</option>
                                <option value="vision_screening" {{ $service->form_type === 'vision_screening' ? 'selected' : '' }}>Vision Screening</option>
                            </select>
                        </div>
                        <div class="w-14">
                            <label class="block text-xs text-gray-500 mb-1">Color</label>
                            <input type="color" name="color" value="{{ $service->color }}" class="w-full h-9 bg-[#141414] border border-white/10 rounded-lg px-1 cursor-pointer">
                        </div>
                        <div class="w-32 min-w-[120px]">
                            <label class="block text-xs text-gray-500 mb-1">Icon</label>
                            <select name="icon" required class="w-full bg-[#141414] border border-white/10 rounded-lg px-2 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                                @foreach($iconOptions as $val => $label)
                                    <option value="{{ $val }}" {{ $service->icon === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label class="flex items-center gap-2 text-xs text-gray-400">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }} class="rounded bg-[#141414] border-white/10 text-[#1392EC] focus:ring-[#1392EC]">
                            Active
                        </label>
                        <button type="submit" class="px-4 py-2 bg-[#1392EC]/10 text-[#1392EC] text-xs font-medium rounded-lg hover:bg-[#1392EC]/20 transition-all">Update</button>
                        @if(!$service->appointments_count)
                        </form>
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('Delete this service?')">
                            @csrf @method('DELETE')
                            <button class="px-4 py-2 bg-red-500/10 text-red-400 text-xs font-medium rounded-lg hover:bg-red-500/20 transition-all">Delete</button>
                        </form>
                        @else
                        </form>
                        @endif
                </div>

                {{-- Reason Presets --}}
                <div class="px-5 py-4">
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Reason Presets</h4>
                    <div class="space-y-2 mb-3">
                        @foreach($service->reasonPresets as $preset)
                        <div class="flex items-center justify-between px-3 py-2 bg-[#141414] rounded-lg">
                            <div class="flex items-center gap-2 flex-1">
                                <span class="material-symbols-outlined text-gray-500" style="font-size:16px;">label</span>
                                <form action="{{ route('admin.services.reason-presets.update', $preset) }}" method="POST" class="flex-1 flex gap-2 items-center">
                                    @csrf @method('PUT')
                                    <input type="text" name="label" value="{{ $preset->label }}" required class="flex-1 bg-transparent border-b border-transparent hover:border-white/10 focus:border-[#1392EC] text-sm text-white py-1 px-1 focus:outline-none transition-colors">
                                    <button type="submit" class="text-gray-500 hover:text-[#1392EC] transition-colors">
                                        <span class="material-symbols-outlined" style="font-size:14px;">check</span>
                                    </button>
                                </form>
                            </div>
                            <form action="{{ route('admin.services.reason-presets.destroy', $preset) }}" method="POST" onsubmit="return confirm('Remove this preset?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-500 hover:text-red-400 transition-colors ml-2">
                                    <span class="material-symbols-outlined" style="font-size:14px;">close</span>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    <form action="{{ route('admin.services.reason-presets.store', $service) }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="label" required class="flex-1 bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]" placeholder="Add a reason preset">
                        <button type="submit" class="px-3 py-2 bg-[#1392EC]/10 text-[#1392EC] text-xs font-medium rounded-lg hover:bg-[#1392EC]/20 transition-all shrink-0">
                            <span class="material-symbols-outlined" style="font-size:14px;">add</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        @if($services->isEmpty())
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl px-5 py-12 text-center">
            <span class="material-symbols-outlined text-gray-600 mb-2" style="font-size:40px;">medical_services</span>
            <p class="text-sm text-gray-500">No services yet</p>
        </div>
        @endif
    </div>
</div>
@endsection
