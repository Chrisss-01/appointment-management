@extends('layouts.app')
@section('title', 'Patients')
@section('page-title', 'Patients')
@section('sidebar') @include('partials.sidebar-staff') @endsection

@section('content')
{{-- Search --}}
<div class="mb-6">
    <form action="{{ route('staff.patients') }}" method="GET" class="relative max-w-md">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" style="font-size:20px;">search</span>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or student ID..." class="w-full bg-[#141414] border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
    </form>
</div>

<div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
    @if($patients->isEmpty())
    <div class="px-5 py-12 text-center">
        <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">person_search</span>
        <p class="text-gray-400 text-sm">No patients found</p>
    </div>
    @else
    <div class="divide-y divide-white/5">
        @foreach($patients as $patient)
        <a href="{{ route('staff.patients.show', $patient) }}" class="block px-5 py-4 hover:bg-white/[0.02] transition-colors">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 text-sm font-bold shrink-0">
                    {{ strtoupper(substr($patient->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white">{{ $patient->name }}</p>
                    <p class="text-xs text-gray-500">
                        {{ $patient->student_id ?? 'No ID' }} · {{ $patient->program ?? '' }} · {{ ucfirst(str_replace('-',' ',$patient->year_level ?? '')) }}
                    </p>
                </div>
                <span class="material-symbols-outlined text-gray-600" style="font-size:18px;">chevron_right</span>
            </div>
        </a>
        @endforeach
    </div>
    <div class="px-5 py-3 border-t border-white/5">{{ $patients->links() }}</div>
    @endif
</div>
@endsection
