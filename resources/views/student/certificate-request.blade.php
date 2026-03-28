@extends('layouts.app')

@section('title', 'Certificate Request')
@section('page-title', 'Certificate Request')
@section('sidebar')
    @include('partials.sidebar-student')
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-lg font-bold text-white">Request a Certificate</h2>
    <p class="text-sm text-gray-500 mt-1">Select a certificate type to begin your request</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($certificateTypes as $type)
    <a href="{{ route('student.certificates.request.form', $type) }}" class="group bg-[#1A1A1A] border border-white/5 rounded-2xl p-6 card-hover block">
        <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: {{ $type->color }}15;">
                <span class="material-symbols-outlined" style="font-size:24px; color:{{ $type->color }};">{{ $type->icon }}</span>
            </div>
            <span class="material-symbols-outlined text-gray-600 group-hover:text-[#1392EC] transition-colors" style="font-size:20px;">arrow_forward</span>
        </div>

        <h3 class="text-base font-semibold text-white mb-2">{{ $type->name }}</h3>
        <p class="text-sm text-gray-500 line-clamp-2">{{ $type->description ?? 'Submit a request for this certificate.' }}</p>

        <div class="flex items-center gap-4 mt-4 pt-4 border-t border-white/5">
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <span class="material-symbols-outlined" style="font-size:14px;">upload_file</span>
                {{ $type->required_documents_count }} required document(s)
            </div>
        </div>
    </a>
    @empty
    <div class="col-span-full text-center py-12">
        <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">description</span>
        <p class="text-gray-400 text-sm">No certificate types available</p>
        <p class="text-xs text-gray-500 mt-1">Check back later</p>
    </div>
    @endforelse
</div>
@endsection
