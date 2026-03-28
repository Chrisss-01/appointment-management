@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('sidebar') @include('partials.sidebar-student') @endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
        {{-- Profile Header --}}
        <div class="px-6 py-6 bg-gradient-to-r from-[#1392EC]/10 to-transparent border-b border-white/5">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#1392EC]/30 to-[#1392EC]/30 border border-[#1392EC]/20 flex items-center justify-center text-[#1392EC] text-2xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-xs text-gray-500">{{ $user->program }}</span>
                        <span class="text-gray-700">·</span>
                        <span class="text-xs text-gray-500 capitalize">{{ str_replace('-', ' ', $user->year_level) }}</span>
                        @if($user->student_id)
                        <span class="text-gray-700">·</span>
                        <span class="text-xs text-[#1392EC]/70 font-mono">{{ $user->student_id }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Form --}}
        <form action="{{ route('student.profile.update') }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC]" required>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="09XX XXX XXXX" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] focus:border-[#1392EC]">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Department</label>
                    <input type="text" value="{{ strtoupper($user->department) }}" class="w-full bg-[#141414] border border-white/5 rounded-xl px-4 py-3 text-sm text-gray-500 cursor-not-allowed" disabled>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Program</label>
                    <input type="text" value="{{ $user->program }}" class="w-full bg-[#141414] border border-white/5 rounded-xl px-4 py-3 text-sm text-gray-500 cursor-not-allowed" disabled>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Year Level</label>
                    <input type="text" value="{{ str_replace('-', ' ', ucwords($user->year_level)) }}" class="w-full bg-[#141414] border border-white/5 rounded-xl px-4 py-3 text-sm text-gray-500 cursor-not-allowed" disabled>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Student ID</label>
                    <input type="text" value="{{ $user->student_id }}" class="w-full bg-[#141414] border border-white/5 rounded-xl px-4 py-3 text-sm text-gray-500 cursor-not-allowed" disabled>
                </div>
            </div>

            <button type="submit" class="px-6 py-2.5 bg-[#1392EC] hover:bg-[#1392EC] text-white text-sm font-medium rounded-xl transition-all shadow-lg shadow-[#1392EC]/20">
                Save Changes
            </button>
        </form>
    </div>
</div>
@endsection
