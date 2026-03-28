@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('sidebar') @include('partials.sidebar-staff') @endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
        <div class="px-6 py-6 bg-gradient-to-r from-[#1392EC]/10 to-transparent border-b border-white/5">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#1392EC]/30 to-[#1392EC]/50 border border-[#1392EC]/20 flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-[#1392EC]/10 text-[#1392EC] capitalize mt-1 inline-block">{{ $user->role }}</span>
                </div>
            </div>
        </div>
        <form action="{{ route('staff.profile.update') }}" method="POST" class="p-6 space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]" required>
            </div>
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC]" placeholder="09XX XXX XXXX">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-[#1392EC] hover:opacity-90 text-white text-sm font-medium rounded-xl transition-all">Save Changes</button>
        </form>
    </div>
</div>
@endsection
