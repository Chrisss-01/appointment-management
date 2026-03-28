@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
{{-- Date Filter --}}
<div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 mb-6">
    <form action="{{ route('admin.reports') }}" method="GET" class="flex items-end gap-4 flex-wrap">
        <div>
            <label class="block text-xs text-gray-400 mb-1.5">Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="bg-[#141414] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
        </div>
        <div>
            <label class="block text-xs text-gray-400 mb-1.5">End Date</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="bg-[#141414] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
        </div>
        <button type="submit" class="px-5 py-2.5 bg-[#1392EC] hover:bg-[#1392EC]/80 text-white text-sm font-medium rounded-xl transition-all">Filter</button>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
    @foreach([
        ['Total', $summary['total'], 'bg-white/5', 'text-white'],
        ['Completed', $summary['completed'], 'bg-[#1392EC]/10', 'text-[#1392EC]'],
        ['Approved', $summary['approved'], 'bg-blue-500/10', 'text-blue-400'],
        ['Pending', $summary['pending'], 'bg-amber-500/10', 'text-amber-400'],
        ['Rejected', $summary['rejected'], 'bg-red-500/10', 'text-red-400'],
        ['Cancelled', $summary['cancelled'], 'bg-gray-500/10', 'text-gray-400'],
        ['No Show', $summary['no_show'], 'bg-red-500/10', 'text-red-300'],
    ] as [$label, $count, $bg, $color])
    <div class="bg-[#1A1A1A] border border-white/5 rounded-xl p-4 text-center">
        <p class="text-xl font-bold {{ $color }}">{{ $count }}</p>
        <p class="text-[10px] text-gray-500 mt-1 uppercase">{{ $label }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- By Service --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-4">By Service</h3>
        <div class="space-y-4">
            @foreach($byService as $svc)
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded" style="background: {{ $svc['color'] }};"></span>
                        <span class="text-sm text-gray-300">{{ $svc['service'] }}</span>
                    </div>
                    <span class="text-sm font-semibold text-white">{{ $svc['total'] }} <span class="text-gray-500 font-normal">({{ $svc['completed'] }} ✓)</span></span>
                </div>
                <div class="w-full bg-white/5 rounded-full h-2">
                    <div class="h-full rounded-full" style="width: {{ $summary['total'] > 0 ? ($svc['total'] / $summary['total']) * 100 : 0 }}%; background: {{ $svc['color'] }};"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- By Staff --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-white mb-4">By Staff</h3>
        <div class="space-y-4">
            @foreach($byStaff as $s)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#1392EC]/10 flex items-center justify-center text-[#1392EC] text-xs font-bold">
                        {{ strtoupper(substr($s['staff'], 0, 1)) }}
                    </div>
                    <span class="text-sm text-gray-300">{{ $s['staff'] }}</span>
                </div>
                <span class="text-sm font-semibold text-white">{{ $s['total'] }} <span class="text-gray-500 font-normal">({{ $s['completed'] }} ✓)</span></span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
