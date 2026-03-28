@extends('layouts.app')
@section('title', 'Staff Management')
@section('page-title', 'Staff Management')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Add Staff --}}
    <div class="lg:col-span-1">
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Add Staff Account</h3>
            <form action="{{ route('admin.staff.create') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Name</label>
                    <input type="text" name="name" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Email</label>
                    <input type="email" name="email" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Password</label>
                    <input type="password" name="password" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Role</label>
                    <select name="role" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Staff Type</label>
                    <select name="staff_type" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        <option value="nurse">Nurse</option>
                        <option value="doctor">Doctor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Phone</label>
                    <input type="text" name="phone" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC]" placeholder="Optional">
                </div>
                <button type="submit" class="w-full py-3 bg-[#1392EC] hover:bg-[#1392EC]/80 text-white text-sm font-semibold rounded-xl transition-all">Create Account</button>
            </form>
        </div>
    </div>

    {{-- Staff List --}}
    <div class="lg:col-span-2">
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/5">
                <h3 class="text-sm font-semibold text-white">Staff Accounts</h3>
            </div>
            @if($staff->isEmpty())
            <div class="px-5 py-12 text-center"><p class="text-gray-400 text-sm">No staff accounts</p></div>
            @else
            <div class="divide-y divide-white/5">
                @foreach($staff as $member)
                <div class="px-5 py-4 flex items-center gap-4 hover:bg-white/[0.02] transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-[#1392EC]/10 flex items-center justify-center text-[#1392EC] text-sm font-bold">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white">{{ $member->name }}</p>
                        <p class="text-xs text-gray-500">{{ $member->email }} · <span class="capitalize">{{ $member->role }}</span>@if($member->staff_type) · <span class="capitalize">{{ $member->staff_type }}</span>@endif</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $member->is_active ? 'bg-[#1392EC]/10 text-[#1392EC]' : 'bg-red-500/10 text-red-400' }}">
                        {{ $member->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.users.toggle-status', $member) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="text-xs px-2 py-1 rounded-lg {{ $member->is_active ? 'bg-red-500/10 text-red-400 hover:bg-red-500/20' : 'bg-[#1392EC]/10 text-[#1392EC] hover:bg-[#1392EC]/20' }} transition-all">
                                {{ $member->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.users.destroy', $member) }}" method="POST" onsubmit="return confirm('Delete this account?')">
                            @csrf @method('DELETE')
                            <button class="text-gray-500 hover:text-red-400 transition-colors">
                                <span class="material-symbols-outlined" style="font-size:16px;">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="px-5 py-3 border-t border-white/5">{{ $staff->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
