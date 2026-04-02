@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div x-data="{
    tab: 'overview',
    mode: '{{ $mode }}',
    init() {
        this.$watch('mode', () => {
            // Reset visibility when mode changes
        });
    }
}">

{{-- Period Filter --}}
<div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 mb-6">
    <form action="{{ route('admin.reports') }}" method="GET">
        {{-- Mode Toggle --}}
        <div class="flex items-center gap-2 mb-4">
            <button type="button" @click="mode = 'monthly'" :class="mode === 'monthly' ? 'bg-[#1392EC] text-white' : 'bg-white/5 text-gray-400 hover:text-white'" class="px-4 py-2 text-xs font-medium rounded-lg transition-all">Monthly</button>
            <button type="button" @click="mode = 'yearly'" :class="mode === 'yearly' ? 'bg-[#1392EC] text-white' : 'bg-white/5 text-gray-400 hover:text-white'" class="px-4 py-2 text-xs font-medium rounded-lg transition-all">Yearly</button>
            <button type="button" @click="mode = 'custom'" :class="mode === 'custom' ? 'bg-[#1392EC] text-white' : 'bg-white/5 text-gray-400 hover:text-white'" class="px-4 py-2 text-xs font-medium rounded-lg transition-all">Custom</button>
        </div>

        <input type="hidden" name="mode" :value="mode">

        <div class="flex items-end gap-4 flex-wrap">
            {{-- Monthly fields --}}
            <div x-show="mode === 'monthly'" class="flex items-end gap-4">
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Month</label>
                    <select name="month" class="bg-[#141414] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ (int) $selectedMonth === $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Year</label>
                    <select name="year" class="bg-[#141414] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        @foreach(range(now()->year, now()->year - 5) as $y)
                            <option value="{{ $y }}" {{ (int) $selectedYear === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Yearly fields --}}
            <div x-show="mode === 'yearly'" class="flex items-end gap-4">
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Year</label>
                    <select name="year" class="bg-[#141414] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        @foreach(range(now()->year, now()->year - 5) as $y)
                            <option value="{{ $y }}" {{ (int) $selectedYear === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Custom fields --}}
            <div x-show="mode === 'custom'" class="flex items-end gap-4">
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="bg-[#141414] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="bg-[#141414] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                </div>
            </div>

            <button type="submit" class="px-5 py-2.5 bg-[#1392EC] hover:bg-[#1392EC]/80 text-white text-sm font-medium rounded-xl transition-all">
                <span class="material-symbols-outlined text-base align-middle mr-1">filter_alt</span> Apply
            </button>
        </div>

        <p class="text-xs text-gray-500 mt-3">
            Showing data from <span class="text-gray-300">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}</span>
            to <span class="text-gray-300">{{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
        </p>
    </form>
</div>

{{-- Tab Navigation --}}
<div class="flex items-center gap-1 mb-6 bg-[#1A1A1A] border border-white/5 rounded-xl p-1">
    @foreach([
        ['overview', 'bar_chart', 'Overview'],
        ['services', 'medical_services', 'By Service'],
        ['cases', 'clinical_notes', 'Common Cases'],
        ['demographics', 'group', 'Student Demographics'],
    ] as [$key, $icon, $label])
    <button @click="tab = '{{ $key }}'" :class="tab === '{{ $key }}' ? 'bg-[#1392EC] text-white' : 'text-gray-400 hover:text-white hover:bg-white/5'" class="flex items-center gap-2 px-4 py-2.5 text-xs font-medium rounded-lg transition-all flex-1 justify-center">
        <span class="material-symbols-outlined text-base">{{ $icon }}</span>
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════════════════════
     TAB: Overview
     ═══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'overview'" x-cloak>
    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        <div class="bg-[#1A1A1A] border border-white/5 rounded-xl p-4 text-center">
            <p class="text-xl font-bold text-white">{{ $summary['total'] }}</p>
            <p class="text-[10px] text-gray-500 mt-1 uppercase">Total Appointments</p>
        </div>
        <div class="bg-[#1A1A1A] border border-white/5 rounded-xl p-4 text-center">
            <p class="text-xl font-bold text-[#1392EC]">{{ $summary['completed'] }}</p>
            <p class="text-[10px] text-gray-500 mt-1 uppercase">Completed Consultations</p>
        </div>
        <div class="bg-[#1A1A1A] border border-white/5 rounded-xl p-4 text-center">
            <p class="text-xl font-bold text-emerald-400">{{ $summary['unique_students'] }}</p>
            <p class="text-[10px] text-gray-500 mt-1 uppercase">Students Served</p>
        </div>
        <div class="bg-[#1A1A1A] border border-white/5 rounded-xl p-4 text-center">
            <p class="text-xl font-bold text-amber-400">{{ $certSummary['total'] }}</p>
            <p class="text-[10px] text-gray-500 mt-1 uppercase">Medical Certificates</p>
            @if($certSummary['approved'] > 0)
            <p class="text-[10px] text-emerald-500 mt-0.5">{{ $certSummary['approved'] }} approved</p>
            @endif
        </div>
        <div class="bg-[#1A1A1A] border border-white/5 rounded-xl p-4 text-center">
            <p class="text-xl font-bold text-purple-400">{{ $completionRate }}%</p>
            <p class="text-[10px] text-gray-500 mt-1 uppercase">Completion Rate</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Daily Trend --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Daily Trend</h3>
            @if($dailyTrend->sum('total') > 0)
                @php $maxDay = $dailyTrend->max('total') ?: 1; @endphp
                <div class="space-y-2 max-h-64 overflow-y-auto pr-2">
                    @foreach($dailyTrend as $day)
                    @if($day['total'] > 0)
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500 w-14 shrink-0">{{ $day['date'] }}</span>
                        <div class="flex-1 flex items-center gap-1.5">
                            <div class="flex-1 bg-white/5 rounded-full h-2">
                                <div class="h-full rounded-full bg-[#1392EC]/60" style="width: {{ ($day['total'] / $maxDay) * 100 }}%;"></div>
                            </div>
                            <span class="text-xs text-gray-400 w-6 text-right">{{ $day['total'] }}</span>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <span class="material-symbols-outlined text-3xl text-gray-600">trending_flat</span>
                    <p class="text-sm text-gray-500 mt-2">No appointment data for this period</p>
                </div>
            @endif
        </div>

        {{-- By Staff --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">By Staff</h3>
            @if($byStaff->sum('total') > 0)
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
            @else
                <div class="text-center py-8">
                    <span class="material-symbols-outlined text-3xl text-gray-600">person_off</span>
                    <p class="text-sm text-gray-500 mt-2">No staff data for this period</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     TAB: By Service
     ═══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'services'" x-cloak>
    @if($byService->sum('total') > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Service Distribution (Donut Chart) --}}
            <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Service Distribution</h3>
                @php
                    $svcTotal = $byService->sum('total');
                    $svcSegments = [];
                    $svcOffset = 0;
                    foreach ($byService as $svc) {
                        $pct = $svcTotal > 0 ? ($svc['total'] / $svcTotal) * 100 : 0;
                        $svcSegments[] = ['label' => $svc['service'], 'total' => $svc['total'], 'completed' => $svc['completed'], 'pct' => $pct, 'offset' => $svcOffset, 'color' => $svc['color']];
                        $svcOffset += $pct;
                    }
                @endphp
                <div class="flex items-center gap-6">
                    {{-- Donut --}}
                    <div class="relative shrink-0" style="width: 140px; height: 140px;">
                        <svg viewBox="0 0 36 36" class="w-full h-full" style="transform: rotate(-90deg);">
                            @foreach($svcSegments as $seg)
                            @if($seg['pct'] > 0)
                            <circle cx="18" cy="18" r="14" fill="none" stroke="{{ $seg['color'] }}" stroke-width="5"
                                stroke-dasharray="{{ $seg['pct'] * 0.8796 }} {{ (100 - $seg['pct']) * 0.8796 }}"
                                stroke-dashoffset="{{ -($seg['offset'] * 0.8796) }}" />
                            @endif
                            @endforeach
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-lg font-bold text-white">{{ $svcTotal }}</span>
                        </div>
                    </div>
                    {{-- Legend --}}
                    <div class="space-y-3 flex-1">
                        @foreach($byService as $svc)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded" style="background: {{ $svc['color'] }};"></span>
                                <span class="text-sm text-gray-300">{{ $svc['service'] }}</span>
                            </div>
                            <span class="text-sm text-white font-medium">{{ $svc['total'] }} <span class="text-gray-500 font-normal">({{ $svc['completed'] }} ✓)</span></span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Completion Rates --}}
            <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Completion Rate by Service</h3>
                <div class="space-y-4">
                    @foreach($byService->sortByDesc('completion_rate') as $svc)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded" style="background: {{ $svc['color'] }};"></span>
                                <span class="text-sm text-gray-300">{{ $svc['service'] }}</span>
                            </div>
                            <span class="text-sm font-semibold text-white">{{ $svc['completion_rate'] }}%</span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-2">
                            <div class="h-full rounded-full bg-emerald-500" style="width: {{ $svc['completion_rate'] }}%;"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Summary Table --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6 mt-6">
            <h3 class="text-sm font-semibold text-white mb-4">Service Summary Table</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="py-3 px-4 text-xs font-medium text-gray-400 uppercase">Service</th>
                            <th class="py-3 px-4 text-xs font-medium text-gray-400 uppercase text-center">Total</th>
                            <th class="py-3 px-4 text-xs font-medium text-gray-400 uppercase text-center">Completed</th>
                            <th class="py-3 px-4 text-xs font-medium text-gray-400 uppercase text-center">Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byService as $svc)
                        <tr class="border-b border-white/5 hover:bg-white/[0.02]">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded" style="background: {{ $svc['color'] }}"></span>
                                    <span class="text-gray-300">{{ $svc['service'] }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center text-white font-medium">{{ $svc['total'] }}</td>
                            <td class="py-3 px-4 text-center text-[#1392EC]">{{ $svc['completed'] }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs {{ $svc['completion_rate'] >= 75 ? 'bg-emerald-500/10 text-emerald-400' : ($svc['completion_rate'] >= 50 ? 'bg-amber-500/10 text-amber-400' : 'bg-red-500/10 text-red-400') }}">
                                    {{ $svc['completion_rate'] }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-12 text-center">
            <span class="material-symbols-outlined text-4xl text-gray-600">medical_services</span>
            <p class="text-sm text-gray-500 mt-3">No service data available for this period</p>
        </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════════
     TAB: Common Cases
     ═══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'cases'" x-cloak>
    @if($topReasons->count() > 0 || $topDiagnoses->count() > 0 || $topCertReasons->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Top Reasons --}}
            <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-1">Top Reasons for Visit</h3>
                <p class="text-xs text-gray-500 mb-4">Based on appointment reason presets</p>
                @if($topReasons->count() > 0)
                    @php $maxReason = $topReasons->max() ?: 1; $totalReasons = $topReasons->sum(); @endphp
                    <div class="space-y-3">
                        @foreach($topReasons as $reason => $count)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-300 capitalize truncate mr-2">{{ $reason }}</span>
                                <span class="text-xs text-gray-400 shrink-0">{{ $count }} <span class="text-gray-600">({{ $totalReasons > 0 ? round(($count / $totalReasons) * 100) : 0 }}%)</span></span>
                            </div>
                            <div class="w-full bg-white/5 rounded-full h-2">
                                <div class="h-full rounded-full bg-amber-500/70" style="width: {{ ($count / $maxReason) * 100 }}%;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-sm text-gray-500">No reason data recorded</p>
                    </div>
                @endif
            </div>

            {{-- Top Diagnoses --}}
            <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-1">Top Diagnoses</h3>
                <p class="text-xs text-gray-500 mb-4">Most common diagnoses given</p>
                @if($topDiagnoses->count() > 0)
                    @php $maxDiagnosis = $topDiagnoses->max('total') ?: 1; $totalDiagnoses = $topDiagnoses->sum('total'); @endphp
                    <div class="space-y-3">
                        @foreach($topDiagnoses as $i => $item)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-300 capitalize truncate mr-2">{{ $item->diagnosis }}</span>
                                <span class="text-xs text-gray-400 shrink-0">{{ $item->total }} <span class="text-gray-600">({{ $totalDiagnoses > 0 ? round(($item->total / $totalDiagnoses) * 100) : 0 }}%)</span></span>
                            </div>
                            <div class="w-full bg-white/5 rounded-full h-2">
                                <div class="h-full rounded-full bg-[#1392EC]/70" style="width: {{ ($item->total / $maxDiagnosis) * 100 }}%;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-sm text-gray-500">No diagnosis data recorded</p>
                    </div>
                @endif
            </div>
        </div>

        @if($topCertReasons->count() > 0)
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6 mt-6">
            <h3 class="text-sm font-semibold text-white mb-1">Top Purposes for Certificate Request</h3>
            <p class="text-xs text-gray-500 mb-4">Based on certificate request purpose presets</p>
            @php $maxCertReason = $topCertReasons->max() ?: 1; $totalCertReasons = $topCertReasons->sum(); @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3">
                @foreach($topCertReasons as $reason => $count)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-300 capitalize truncate mr-2">{{ $reason }}</span>
                        <span class="text-xs text-gray-400 shrink-0">{{ $count }} <span class="text-gray-600">({{ $totalCertReasons > 0 ? round(($count / $totalCertReasons) * 100) : 0 }}%)</span></span>
                    </div>
                    <div class="w-full bg-white/5 rounded-full h-2">
                        <div class="h-full rounded-full bg-emerald-500/70" style="width: {{ ($count / $maxCertReason) * 100 }}%;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @else
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-12 text-center">
            <span class="material-symbols-outlined text-4xl text-gray-600">clinical_notes</span>
            <p class="text-sm text-gray-500 mt-3">No common cases data found for completed appointments in this period</p>
            <p class="text-xs text-gray-600 mt-1">Reasons are sourced from appointment presets, diagnoses from medical records</p>
        </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════════
     TAB: Student Demographics
     ═══════════════════════════════════════════════════════════════ --}}
<div x-show="tab === 'demographics'" x-cloak>
    @if($summary['unique_students'] > 0)
        {{-- Totals header --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-emerald-400">group</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $summary['unique_students'] }}</p>
                    <p class="text-xs text-gray-500">Unique students served in this period</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Gender Distribution (Donut Chart) --}}
            <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4">By Gender</h3>
                @php
                    $genderColors = ['male' => '#3B82F6', 'female' => '#EC4899', 'other' => '#8B5CF6'];
                    $genderTotal = $byGender->sum();
                    $genderSegments = [];
                    $genderOffset = 0;
                    foreach ($byGender as $g => $c) {
                        $pct = $genderTotal > 0 ? ($c / $genderTotal) * 100 : 0;
                        $genderSegments[] = ['label' => $g, 'count' => $c, 'pct' => $pct, 'offset' => $genderOffset, 'color' => $genderColors[strtolower($g)] ?? '#6B7280'];
                        $genderOffset += $pct;
                    }
                @endphp
                <div class="flex items-center gap-6">
                    {{-- Donut --}}
                    <div class="relative shrink-0" style="width: 140px; height: 140px;">
                        <svg viewBox="0 0 36 36" class="w-full h-full" style="transform: rotate(-90deg);">
                            @foreach($genderSegments as $seg)
                            <circle cx="18" cy="18" r="14" fill="none" stroke="{{ $seg['color'] }}" stroke-width="5"
                                stroke-dasharray="{{ $seg['pct'] * 0.8796 }} {{ (100 - $seg['pct']) * 0.8796 }}"
                                stroke-dashoffset="{{ -($seg['offset'] * 0.8796) }}" />
                            @endforeach
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-lg font-bold text-white">{{ $genderTotal }}</span>
                        </div>
                    </div>
                    {{-- Legend --}}
                    <div class="space-y-3 flex-1">
                        @foreach($byGender as $gender => $count)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full" style="background: {{ $genderColors[strtolower($gender)] ?? '#6B7280' }}"></span>
                                <span class="text-sm text-gray-300 capitalize">{{ $gender ?: 'Not specified' }}</span>
                            </div>
                            <span class="text-sm text-white font-medium">{{ $count }} <span class="text-gray-500 font-normal">({{ $genderTotal > 0 ? round(($count / $genderTotal) * 100) : 0 }}%)</span></span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Age Distribution --}}
            <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4">By Age Group</h3>
                @php $maxAge = $byAge->max() ?: 1; $ageTotal = $byAge->sum(); @endphp
                <div class="space-y-3">
                    @foreach($byAge as $bracket => $count)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-300">{{ $bracket }}</span>
                            <span class="text-xs text-gray-400">{{ $count }} <span class="text-gray-600">({{ $ageTotal > 0 ? round(($count / $ageTotal) * 100) : 0 }}%)</span></span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-2">
                            <div class="h-full rounded-full bg-violet-500/70" style="width: {{ ($count / $maxAge) * 100 }}%;"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Department Distribution --}}
            <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4">By Department</h3>
                @php
                    $deptColors = ['College of Education' => '#F59E0B', 'College of Business Administration' => '#10B981', 'College of Engineering, Technology & Architecture' => '#3B82F6', 'College of Criminal Justice Education' => '#EF4444', 'Senior High School' => '#8B5CF6'];
                    $maxDept = $byDepartment->max() ?: 1;
                    $deptTotal = $byDepartment->sum();
                @endphp
                <div class="space-y-3">
                    @foreach($byDepartment as $dept => $count)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-300 truncate mr-2">{{ $dept }}</span>
                            <span class="text-xs text-gray-400 shrink-0">{{ $count }} <span class="text-gray-600">({{ $deptTotal > 0 ? round(($count / $deptTotal) * 100) : 0 }}%)</span></span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-2">
                            <div class="h-full rounded-full" style="width: {{ ($count / $maxDept) * 100 }}%; background: {{ $deptColors[$dept] ?? '#6B7280' }};"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Year Level Distribution --}}
            <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4">By Year Level</h3>
                @php $maxYl = $byYearLevel->max() ?: 1; $ylTotal = $byYearLevel->sum(); @endphp
                <div class="space-y-3">
                    @foreach($byYearLevel as $yl => $count)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-300">{{ $yl }}</span>
                            <span class="text-xs text-gray-400">{{ $count }} <span class="text-gray-600">({{ $ylTotal > 0 ? round(($count / $ylTotal) * 100) : 0 }}%)</span></span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-2">
                            <div class="h-full rounded-full bg-cyan-500/70" style="width: {{ ($count / $maxYl) * 100 }}%;"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Program Distribution (full width) --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6 mt-6">
            <h3 class="text-sm font-semibold text-white mb-4">By Program</h3>
            @php $maxProg = $byProgram->max() ?: 1; $progTotal = $byProgram->sum(); @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3">
                @foreach($byProgram as $prog => $count)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-300">{{ $prog }}</span>
                        <span class="text-xs text-gray-400">{{ $count }} <span class="text-gray-600">({{ $progTotal > 0 ? round(($count / $progTotal) * 100) : 0 }}%)</span></span>
                    </div>
                    <div class="w-full bg-white/5 rounded-full h-2">
                        <div class="h-full rounded-full bg-[#1392EC]/70" style="width: {{ ($count / $maxProg) * 100 }}%;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-12 text-center">
            <span class="material-symbols-outlined text-4xl text-gray-600">group</span>
            <p class="text-sm text-gray-500 mt-3">No students were served in this period</p>
            <p class="text-xs text-gray-600 mt-1">Demographics data is based on completed appointments</p>
        </div>
    @endif
</div>

</div>
@endsection
