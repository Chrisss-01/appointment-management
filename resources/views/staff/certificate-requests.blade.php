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
        <a href="{{ route('staff.certificate-requests', ['status' => $key]) }}"
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
    <div class="px-5 py-3 border-t border-white/5">{{ $certificateRequests->appends(['status' => $status])->links() }}</div>
    @endif
</div>
@endsection
