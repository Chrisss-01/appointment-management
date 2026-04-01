@extends('layouts.app')

@section('title', 'Certificate Requests')
@section('page-title', 'Certificate Requests')
@section('sidebar')
    @include('partials.sidebar-staff')
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-lg font-bold text-white">Certificate Requests</h2>
    <p class="text-sm text-gray-500 mt-1">Review and manage student certificate requests</p>
</div>

{{-- Search Bar --}}
<form method="GET" action="{{ route('staff.certificate-requests') }}" class="mb-4">
    <input type="hidden" name="status" value="{{ $status }}">
    <div class="flex gap-2">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" style="font-size:18px;">search</span>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by student name or ID..."
                   class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
        </div>
        <button type="submit" class="px-4 py-2.5 bg-[#1392EC] text-white text-sm font-medium rounded-xl hover:bg-[#1392EC]/90 transition-all">Search</button>
        @if($search)
            <a href="{{ route('staff.certificate-requests', ['status' => $status]) }}" class="px-4 py-2.5 bg-[#1A1A1A] border border-white/10 text-gray-400 text-sm font-medium rounded-xl hover:text-white transition-all">Clear</a>
        @endif
    </div>
</form>

{{-- Status Tabs --}}
<div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1">
    @php
        $tabs = [
            'pending' => ['label' => 'Pending', 'icon' => 'schedule'],
            'documents_verified' => ['label' => 'Docs Verified', 'icon' => 'verified'],
            'approved' => ['label' => 'Approved', 'icon' => 'check_circle'],
            'rejected' => ['label' => 'Rejected', 'icon' => 'cancel'],
            'all' => ['label' => 'All', 'icon' => 'list'],
        ];
    @endphp
    @foreach($tabs as $key => $tab)
        <a href="{{ route('staff.certificate-requests', array_filter(['status' => $key, 'search' => $search])) }}"
           class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium transition-all whitespace-nowrap
                  {{ $status === $key ? 'bg-[#1392EC]/10 text-[#1392EC] border border-[#1392EC]/20' : 'bg-[#1A1A1A] text-gray-400 border border-white/5 hover:border-white/10 hover:text-white' }}">
            <span class="material-symbols-outlined" style="font-size:16px;">{{ $tab['icon'] }}</span>
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>

{{-- Request List --}}
<div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
    @if($certificateRequests->isEmpty())
    <div class="px-5 py-12 text-center">
        <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">description</span>
        <p class="text-gray-400 text-sm">No certificate requests found</p>
    </div>
    @else
    <div class="divide-y divide-white/5">
        @foreach($certificateRequests as $req)
        <a href="{{ route('staff.certificate-requests.show', $req) }}" class="block px-5 py-4 hover:bg-white/[0.02] transition-colors">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: {{ $req->certificateType->color ?? '#F59E0B' }}15;">
                        <span class="material-symbols-outlined" style="font-size:20px; color: {{ $req->certificateType->color ?? '#F59E0B' }};">{{ $req->certificateType->icon ?? 'description' }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white">{{ $req->student->name }}</p>
                        <p class="text-xs text-gray-500">{{ $req->certificateType->name }} · {{ $req->created_at->format('M d, Y g:i A') }}</p>
                        @if($req->purpose)
                            <p class="text-xs text-gray-600 mt-0.5">{{ Str::limit($req->purpose, 50) }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @php
                        $statusColors = [
                            'pending' => 'bg-amber-500/10 text-amber-400',
                            'documents_verified' => 'bg-blue-500/10 text-blue-400',
                            'approved' => 'bg-emerald-500/10 text-emerald-400',
                            'rejected' => 'bg-red-500/10 text-red-400',
                        ];
                        $statusLabels = [
                            'pending' => 'Pending',
                            'documents_verified' => 'Docs Verified',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ];
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $statusColors[$req->status] ?? '' }}">
                        {{ $statusLabels[$req->status] ?? $req->status }}
                    </span>
                    <span class="material-symbols-outlined text-gray-600" style="font-size:16px;">chevron_right</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="px-5 py-3 border-t border-white/5">{{ $certificateRequests->appends(array_filter(['status' => $status, 'search' => $search]))->links() }}</div>
    @endif
</div>
@endsection
