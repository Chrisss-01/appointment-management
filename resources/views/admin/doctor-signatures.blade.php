@extends('layouts.app')
@section('title', 'Doctor Signatures')
@section('page-title', 'Doctor Signature Management')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="space-y-4">
    @forelse($doctors as $doctor)
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
        <div class="flex items-start gap-6">
            {{-- Doctor Info --}}
            <div class="flex items-center gap-4 flex-1">
                <div class="w-12 h-12 rounded-xl bg-[#1392EC]/10 flex items-center justify-center text-[#1392EC] text-lg font-bold shrink-0">
                    {{ strtoupper(substr($doctor->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-white">{{ $doctor->name }}</p>
                    <p class="text-xs text-gray-500">{{ $doctor->email }}</p>
                    @if($doctor->license_number)
                    <p class="text-xs text-gray-400 mt-1">License: <span class="font-mono">{{ $doctor->license_number }}</span></p>
                    @endif
                </div>
            </div>

            {{-- Current Signature Preview --}}
            <div class="shrink-0">
                @if($doctor->signature_image)
                <div class="w-40 h-20 border border-white/10 rounded-xl bg-white flex items-center justify-center overflow-hidden">
                    <img src="{{ Storage::url($doctor->signature_image) }}" alt="Signature" class="max-w-full max-h-full object-contain">
                </div>
                @else
                <div class="w-40 h-20 border border-white/10 rounded-xl bg-[#141414] flex items-center justify-center">
                    <span class="text-xs text-gray-500">No signature</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Update Form --}}
        <form action="{{ route('admin.doctor-signatures.update', $doctor) }}" method="POST" enctype="multipart/form-data" class="mt-4 pt-4 border-t border-white/5">
            @csrf @method('PUT')
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs text-gray-400 mb-1.5">License Number</label>
                    <input type="text" name="license_number" value="{{ $doctor->license_number }}" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]" placeholder="e.g. 0123456">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs text-gray-400 mb-1.5">Signature Image</label>
                    <input type="file" name="signature_image" accept="image/*" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-400 file:mr-3 file:px-3 file:py-1 file:rounded-lg file:border-0 file:bg-[#1392EC]/10 file:text-[#1392EC] file:text-xs file:font-medium file:cursor-pointer focus:outline-none">
                </div>
                <button type="submit" class="px-6 py-3 bg-[#1392EC] hover:bg-[#1392EC]/80 text-white text-sm font-semibold rounded-xl transition-all shrink-0">
                    Update
                </button>
            </div>
        </form>
    </div>
    @empty
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl px-5 py-12 text-center">
        <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">draw</span>
        <p class="text-sm text-gray-400 mb-1">No doctors found</p>
        <p class="text-xs text-gray-500">Create a staff account with the "Doctor" type first</p>
    </div>
    @endforelse
</div>
@endsection
