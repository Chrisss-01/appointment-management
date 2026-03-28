@extends('layouts.app')
@section('title', 'Certificate Types')
@section('page-title', 'Certificate Type Management')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Add Type --}}
    <div class="lg:col-span-1">
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Add Certificate Type</h3>
            <form action="{{ route('admin.certificate-types.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Name</label>
                    <input type="text" name="name" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]" placeholder="e.g. Medical Certificate">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Description</label>
                    <textarea name="description" rows="2" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none" placeholder="Short description of this certificate"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">Color</label>
                        <input type="color" name="color" value="#1392EC" class="w-full h-11 bg-[#141414] border border-white/10 rounded-xl px-2 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">Icon</label>
                        <input type="text" name="icon" value="description" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]" placeholder="Material icon name">
                    </div>
                </div>
                <button type="submit" class="w-full py-3 bg-[#1392EC] hover:bg-[#1392EC]/80 text-white text-sm font-semibold rounded-xl transition-all">Add Certificate Type</button>
            </form>
        </div>
    </div>

    {{-- Type List --}}
    <div class="lg:col-span-2 space-y-4">
        @foreach($certificateTypes as $type)
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden" x-data="{ open: false }">
            {{-- Type Header --}}
            <div class="px-5 py-4 flex items-center gap-4 cursor-pointer" @click="open = !open">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: {{ $type->color }}15;">
                    <span class="material-symbols-outlined" style="font-size:20px; color: {{ $type->color }};">{{ $type->icon }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white">{{ $type->name }}</p>
                    <p class="text-xs text-gray-500">{{ $type->certificate_requests_count }} requests · {{ $type->requiredDocuments->count() }} docs · {{ $type->purposePresets->count() }} purposes</p>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $type->is_active ? 'bg-[#1392EC]/10 text-[#1392EC]' : 'bg-red-500/10 text-red-400' }}">
                    {{ $type->is_active ? 'Active' : 'Inactive' }}
                </span>
                <span class="material-symbols-outlined text-gray-500 transition-transform" :class="open && 'rotate-180'" style="font-size:18px;">expand_more</span>
            </div>

            {{-- Expandable Detail --}}
            <div x-show="open" x-cloak class="border-t border-white/5">
                {{-- Edit Type --}}
                <div class="px-5 py-4 border-b border-white/5">
                    <form action="{{ route('admin.certificate-types.update', $type) }}" method="POST" class="flex flex-wrap gap-3 items-end">
                        @csrf @method('PUT')
                        <div class="flex-1 min-w-[120px]">
                            <label class="block text-xs text-gray-500 mb-1">Name</label>
                            <input type="text" name="name" value="{{ $type->name }}" required class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        </div>
                        <div class="flex-1 min-w-[120px]">
                            <label class="block text-xs text-gray-500 mb-1">Description</label>
                            <input type="text" name="description" value="{{ $type->description }}" class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        </div>
                        <div class="w-14">
                            <label class="block text-xs text-gray-500 mb-1">Color</label>
                            <input type="color" name="color" value="{{ $type->color }}" class="w-full h-9 bg-[#141414] border border-white/10 rounded-lg px-1 cursor-pointer">
                        </div>
                        <div class="w-24">
                            <label class="block text-xs text-gray-500 mb-1">Icon</label>
                            <input type="text" name="icon" value="{{ $type->icon }}" class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        </div>
                        <label class="flex items-center gap-2 text-xs text-gray-400">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ $type->is_active ? 'checked' : '' }} class="rounded bg-[#141414] border-white/10 text-[#1392EC] focus:ring-[#1392EC]">
                            Active
                        </label>
                        <button type="submit" class="px-4 py-2 bg-[#1392EC]/10 text-[#1392EC] text-xs font-medium rounded-lg hover:bg-[#1392EC]/20 transition-all">Update</button>
                        @if(!$type->certificate_requests_count)
                        </form>
                        <form action="{{ route('admin.certificate-types.destroy', $type) }}" method="POST" class="inline" onsubmit="return confirm('Delete this certificate type?')">
                            @csrf @method('DELETE')
                            <button class="px-4 py-2 bg-red-500/10 text-red-400 text-xs font-medium rounded-lg hover:bg-red-500/20 transition-all">Delete</button>
                        </form>
                        @else
                        </form>
                        @endif
                </div>

                {{-- Required Documents --}}
                <div class="px-5 py-4 border-b border-white/5">
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Required Documents</h4>
                    <div class="space-y-2 mb-3">
                        @foreach($type->requiredDocuments as $doc)
                        <div class="flex items-center justify-between px-3 py-2 bg-[#141414] rounded-lg">
                            <div>
                                <span class="text-sm text-white">{{ $doc->name }}</span>
                                @if($doc->description)
                                    <span class="text-xs text-gray-500 ml-2">{{ $doc->description }}</span>
                                @endif
                                @if($doc->is_required)
                                    <span class="text-[10px] text-amber-400 ml-1">Required</span>
                                @else
                                    <span class="text-[10px] text-gray-500 ml-1">Optional</span>
                                @endif
                            </div>
                            <form action="{{ route('admin.certificate-types.documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Remove this document requirement?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-500 hover:text-red-400 transition-colors">
                                    <span class="material-symbols-outlined" style="font-size:14px;">close</span>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    <form action="{{ route('admin.certificate-types.documents.store', $type) }}" method="POST" class="flex gap-2 items-end">
                        @csrf
                        <div class="flex-1">
                            <input type="text" name="name" required class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]" placeholder="Document name">
                        </div>
                        <div class="flex-1">
                            <input type="text" name="description" class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC]" placeholder="Description (opt)">
                        </div>
                        <label class="flex items-center gap-1 text-xs text-gray-400 shrink-0">
                            <input type="checkbox" name="is_required" value="1" checked class="rounded bg-[#141414] border-white/10 text-[#1392EC] focus:ring-[#1392EC]">
                            Req
                        </label>
                        <button type="submit" class="px-3 py-2 bg-[#1392EC]/10 text-[#1392EC] text-xs font-medium rounded-lg hover:bg-[#1392EC]/20 transition-all shrink-0">
                            <span class="material-symbols-outlined" style="font-size:14px;">add</span>
                        </button>
                    </form>
                </div>

                {{-- Purpose Presets --}}
                <div class="px-5 py-4">
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Purpose Presets</h4>
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($type->purposePresets as $purpose)
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-[#141414] border border-white/5 rounded-lg">
                            <span class="text-sm text-white">{{ $purpose->label }}</span>
                            <form action="{{ route('admin.certificate-types.purposes.destroy', $purpose) }}" method="POST" onsubmit="return confirm('Remove?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-500 hover:text-red-400 transition-colors">
                                    <span class="material-symbols-outlined" style="font-size:12px;">close</span>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    <form action="{{ route('admin.certificate-types.purposes.store', $type) }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="label" required class="flex-1 bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]" placeholder="Add a purpose preset">
                        <button type="submit" class="px-3 py-2 bg-[#1392EC]/10 text-[#1392EC] text-xs font-medium rounded-lg hover:bg-[#1392EC]/20 transition-all shrink-0">
                            <span class="material-symbols-outlined" style="font-size:14px;">add</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        @if($certificateTypes->isEmpty())
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl px-5 py-12 text-center">
            <span class="material-symbols-outlined text-gray-600 mb-2" style="font-size:40px;">description</span>
            <p class="text-sm text-gray-500">No certificate types yet</p>
        </div>
        @endif
    </div>
</div>
@endsection
