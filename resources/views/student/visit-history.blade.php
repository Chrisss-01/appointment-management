@extends('layouts.app')
@section('title', 'Visit History')
@section('page-title', 'Visit History')
@section('sidebar') @include('partials.sidebar-student') @endsection

@section('content')
<div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-white/5">
        <h3 class="text-sm font-semibold text-white">All Visits</h3>
    </div>
    @if($visits->isEmpty())
    <div class="px-5 py-12 text-center">
        <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">history</span>
        <p class="text-gray-400 text-sm">No visit history</p>
    </div>
    @else
    <div class="divide-y divide-white/5">
        @foreach($visits as $visit)
        <div class="px-5 py-4 flex items-center gap-4 hover:bg-white/[0.02] transition-colors">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: {{ $visit->service->color }}15;">
                <span class="material-symbols-outlined" style="font-size:20px; color: {{ $visit->service->color }};">medical_services</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white">{{ $visit->service->name }}</p>
                <p class="text-xs text-gray-500">{{ $visit->date->format('M d, Y') }} · {{ \Carbon\Carbon::parse($visit->start_time)->format('g:i A') }} · {{ optional($visit->staff)->name ?? 'N/A' }}</p>
            </div>
            @php $c = ['pending'=>'bg-amber-500/10 text-amber-400','approved'=>'bg-[#1392EC]/10 text-[#1392EC]','rejected'=>'bg-red-500/10 text-red-400','completed'=>'bg-blue-500/10 text-blue-400','cancelled'=>'bg-gray-500/10 text-gray-400','no_show'=>'bg-red-500/10 text-red-400']; @endphp
            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $c[$visit->status] ?? '' }}">{{ str_replace('_',' ',$visit->status) }}</span>
        </div>
        @endforeach
    </div>
    <div class="px-5 py-3 border-t border-white/5">{{ $visits->links() }}</div>
    @endif
</div>
@endsection
