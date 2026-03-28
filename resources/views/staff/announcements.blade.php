@extends('layouts.app')
@section('title', 'Announcements')
@section('page-title', 'Announcements')
@section('sidebar') @include('partials.sidebar-staff') @endsection

@section('content')
<div class="space-y-4">
    @forelse($announcements as $ann)
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-5 card-hover">
        <h3 class="text-base font-semibold text-white">{{ $ann->title }}</h3>
        <p class="text-xs text-gray-500 mt-1">By {{ $ann->author->name }} · {{ $ann->published_at?->diffForHumans() }}</p>
        <div class="mt-3 text-sm text-gray-400 leading-relaxed">{!! nl2br(e($ann->content)) !!}</div>
    </div>
    @empty
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-12 text-center">
        <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">campaign</span>
        <p class="text-gray-400 text-sm">No announcements</p>
    </div>
    @endforelse
    <div class="mt-4">{{ $announcements->links() }}</div>
</div>
@endsection
