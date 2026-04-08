@extends('layouts.app')

@section('title', 'Medical Services')
@section('page-title', 'Medical Services')
@section('sidebar')
    @include('partials.sidebar-student')
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-lg font-bold text-white">Choose a Service</h2>
    <p class="text-sm text-gray-500 mt-1">Select a medical service to book an appointment</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @foreach($services as $service)
    <a href="{{ route('student.services.show', $service) }}" class="group bg-[#1A1A1A] border border-white/5 rounded-2xl p-6 card-hover block">
        <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: {{ $service->color }}15;">
                <span class="material-symbols-outlined" style="font-size:24px; color:{{ $service->color }};">{{ $service->icon ?? 'medical_services' }}</span>
            </div>
            <span class="material-symbols-outlined text-gray-600 group-hover:text-[#1392EC] transition-colors" style="font-size:20px;">arrow_forward</span>
        </div>

        <h3 class="text-base font-semibold text-white mb-2">{{ $service->name }}</h3>
        <p class="text-sm text-gray-500 line-clamp-2">{{ $service->description }}</p>

        <div class="flex items-center gap-4 mt-4 pt-4 border-t border-white/5">
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <span class="material-symbols-outlined" style="font-size:14px;">schedule</span>
                {{ $service->duration_minutes }} min / session
            </div>
            @if($service->isAvailable())
            <div class="flex items-center gap-1.5 text-xs text-green-500">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                Available
            </div>
            @else
            <div class="flex items-center gap-1.5 text-xs text-red-400">
                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                Unavailable
            </div>
            @endif
        </div>
    </a>
    @endforeach
</div>
@endsection
