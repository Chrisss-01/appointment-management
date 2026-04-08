@extends('layouts.app')

@section('title', 'My Certificates')
@section('page-title', 'My Certificates')
@section('sidebar')
    @include('partials.sidebar-student')
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-bold text-white">My Certificates</h2>
        <p class="text-sm text-gray-500 mt-1">Track your certificate requests and download approved certificates</p>
    </div>
    <a href="{{ route('student.certificates.request') }}" class="flex items-center gap-2 px-4 py-2.5 bg-[#1392EC] hover:bg-[#1392EC]/90 text-white text-sm font-medium rounded-xl transition-all shadow-lg shadow-[#1392EC]/20">
        <span class="material-symbols-outlined" style="font-size:18px;">add</span>
        New Request
    </a>
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
        <a href="{{ route('student.certificates.my', ['status' => $key]) }}"
           class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium transition-all whitespace-nowrap
                  {{ $status === $key ? 'bg-[#1392EC]/10 text-[#1392EC] border border-[#1392EC]/20' : 'bg-[#1A1A1A] text-gray-400 border border-white/5 hover:border-white/10 hover:text-white' }}">
            <span class="material-symbols-outlined" style="font-size:16px;">{{ $tab['icon'] }}</span>
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>

<div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-white/5">
        <h3 class="text-sm font-semibold text-white">
            @if($status === 'all')
                All Certificate Requests
            @else
                {{ $tabs[$status]['label'] }} Requests
            @endif
        </h3>
    </div>

    @if($certificates->isEmpty())
    <div class="px-5 py-12 text-center">
        <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">description</span>
        <p class="text-gray-400 text-sm">No {{ $status !== 'all' ? strtolower($tabs[$status]['label']) : '' }} certificate requests found</p>
        @if($status === 'all')
            <a href="{{ route('student.certificates.request') }}" class="text-xs text-[#1392EC] hover:text-[#1392EC]/80 mt-2 inline-block">Request one now →</a>
        @endif
    </div>
    @else
    <div class="divide-y divide-white/5">
        @foreach($certificates as $cert)
        <div class="px-5 py-4 hover:bg-white/[0.02] transition-colors">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: {{ $cert->certificateType->color ?? '#F59E0B' }}15;">
                        <span class="material-symbols-outlined" style="font-size:20px; color: {{ $cert->certificateType->color ?? '#F59E0B' }};">{{ $cert->certificateType->icon ?? 'description' }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white">{{ $cert->certificateType->name ?? 'Certificate' }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $cert->created_at->format('M d, Y') }}
                            @if($cert->purpose) · {{ Str::limit($cert->purpose, 40) }} @endif
                        </p>
                        @if($cert->certificate_number)
                            <p class="text-[10px] text-gray-600 font-mono mt-0.5">{{ $cert->certificate_number }}</p>
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
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $statusColors[$cert->status] ?? '' }}">
                        {{ $statusLabels[$cert->status] ?? $cert->status }}
                    </span>
                    @if($cert->isApproved() && $cert->file_path)
                        <a href="{{ route('student.certificates.download', $cert) }}" class="flex items-center gap-1 px-3 py-1.5 bg-[#1392EC]/10 text-[#1392EC] text-xs font-medium rounded-lg hover:bg-[#1392EC]/20 transition-all">
                            <span class="material-symbols-outlined" style="font-size:14px;">download</span>
                            Download
                        </a>
                    @endif
                </div>
            </div>
            @if($cert->isRejected() && $cert->rejection_reason)
                <div class="mt-2 ml-13 px-3 py-2 bg-red-500/5 border border-red-500/10 rounded-lg">
                    <p class="text-xs text-red-400"><strong>Reason:</strong> {{ $cert->rejection_reason }}</p>
                </div>
            @endif
        </div>
        @endforeach
    </div>
    <div class="px-5 py-3 border-t border-white/5">{{ $certificates->appends(['status' => $status])->links() }}</div>
    @endif
</div>
@endsection
