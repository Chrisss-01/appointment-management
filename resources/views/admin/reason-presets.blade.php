@extends('layouts.app')
@section('title', 'Reason Presets')
@section('page-title', 'Reason Preset Management')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Add Preset --}}
    <div class="lg:col-span-1">
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Add Reason Preset</h3>
            <form action="{{ route('admin.reason-presets.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Service</label>
                    <select name="service_id" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        <option value="">Select a service</option>
                        @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Reason Label</label>
                    <input type="text" name="label" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]" placeholder="e.g. Headache">
                </div>
                <button type="submit" class="w-full py-3 bg-[#1392EC] hover:bg-[#1392EC]/80 text-white text-sm font-semibold rounded-xl transition-all">Add Preset</button>
            </form>
        </div>
    </div>

    {{-- Presets by Service --}}
    <div class="lg:col-span-2 space-y-4">
        @foreach($services as $service)
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: {{ $service->color }}15;">
                    <span class="w-3 h-3 rounded" style="background: {{ $service->color }};"></span>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white">{{ $service->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $service->reasonPresets->count() }} preset(s)</p>
                </div>
            </div>
            @if($service->reasonPresets->isEmpty())
            <div class="px-5 py-6 text-center">
                <p class="text-xs text-gray-500">No reason presets for this service</p>
            </div>
            @else
            <div class="divide-y divide-white/5">
                @foreach($service->reasonPresets as $preset)
                <div class="px-5 py-3 flex items-center gap-3 hover:bg-white/[0.02] transition-colors">
                    <span class="material-symbols-outlined text-gray-500" style="font-size:16px;">label</span>
                    <form action="{{ route('admin.reason-presets.update', $preset) }}" method="POST" class="flex-1 flex gap-2 items-center">
                        @csrf @method('PUT')
                        <input type="text" name="label" value="{{ $preset->label }}" required class="flex-1 bg-transparent border-b border-transparent hover:border-white/10 focus:border-[#1392EC] text-sm text-white py-1 px-1 focus:outline-none transition-colors">
                        <button type="submit" class="text-gray-500 hover:text-[#1392EC] transition-colors">
                            <span class="material-symbols-outlined" style="font-size:14px;">check</span>
                        </button>
                    </form>
                    <form action="{{ route('admin.reason-presets.destroy', $preset) }}" method="POST" onsubmit="return confirm('Remove this preset?')">
                        @csrf @method('DELETE')
                        <button class="text-gray-500 hover:text-red-400 transition-colors">
                            <span class="material-symbols-outlined" style="font-size:14px;">close</span>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach

        @if($services->isEmpty())
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl px-5 py-12 text-center">
            <span class="material-symbols-outlined text-gray-600 mb-2" style="font-size:40px;">list</span>
            <p class="text-sm text-gray-500">No services found</p>
        </div>
        @endif
    </div>
</div>
@endsection
