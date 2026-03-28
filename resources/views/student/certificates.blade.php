@extends('layouts.app')
@section('title', 'My Certificates')
@section('page-title', 'My Certificates')
@section('sidebar') @include('partials.sidebar-student') @endsection

@section('content')
<div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-white/5">
        <h3 class="text-sm font-semibold text-white">Certificate Requests</h3>
    </div>
    @if($certificates->isEmpty())
    <div class="px-5 py-12 text-center">
        <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">description</span>
        <p class="text-gray-400 text-sm">No certificate requests yet</p>
        <p class="text-xs text-gray-500 mt-1">Book a Medical Certificate Request appointment to get started</p>
    </div>
    @else
    <div class="divide-y divide-white/5">
        @foreach($certificates as $cert)
        <div class="px-5 py-4 hover:bg-white/[0.02] transition-colors">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-amber-400" style="font-size:20px;">description</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white capitalize">{{ str_replace('_',' ',$cert->certificate_type) }}</p>
                        <p class="text-xs text-gray-500">{{ $cert->created_at->format('M d, Y') }} {{ $cert->purpose ? '· '.$cert->purpose : '' }}</p>
                    </div>
                </div>
                @php $c = ['pending'=>'bg-amber-500/10 text-amber-400','processing'=>'bg-blue-500/10 text-blue-400','ready'=>'bg-[#1392EC]/10 text-[#1392EC]','released'=>'bg-blue-500/10 text-blue-400','rejected'=>'bg-red-500/10 text-red-400']; @endphp
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $c[$cert->status] ?? '' }}">{{ $cert->status }}</span>
            </div>
        </div>
        @endforeach
    </div>
    <div class="px-5 py-3 border-t border-white/5">{{ $certificates->links() }}</div>
    @endif
</div>
@endsection
