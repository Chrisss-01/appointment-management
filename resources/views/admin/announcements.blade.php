@extends('layouts.app')
@section('title', 'Announcements')
@section('page-title', 'Announcements')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Create --}}
    <div class="lg:col-span-1">
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">New Announcement</h3>
            <form action="{{ route('admin.announcements.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Title</label>
                    <input type="text" name="title" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Content</label>
                    <textarea name="content" rows="4" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Target Audience</label>
                    <select name="target_audience" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                        <option value="all">All</option>
                        <option value="students">Students Only</option>
                        <option value="staff">Staff Only</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Expires At (optional)</label>
                    <input type="date" name="expires_at" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" checked class="w-4 h-4 rounded bg-[#141414] text-[#1392EC] border-white/10 focus:ring-[#1392EC]">
                    <span class="text-sm text-gray-400">Publish immediately</span>
                </label>
                <button type="submit" class="w-full py-3 bg-[#1392EC] hover:bg-[#1392EC] text-white text-sm font-semibold rounded-xl transition-all">Post Announcement</button>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="lg:col-span-2">
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/5"><h3 class="text-sm font-semibold text-white">All Announcements</h3></div>
            @if($announcements->isEmpty())
            <div class="px-5 py-12 text-center"><p class="text-gray-400 text-sm">No announcements</p></div>
            @else
            <div class="divide-y divide-white/5">
                @foreach($announcements as $ann)
                <div class="px-5 py-4 hover:bg-white/[0.02] transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-white">{{ $ann->title }}</h4>
                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ Str::limit($ann->content, 100) }}</p>
                            <p class="text-[10px] text-gray-600 mt-1">{{ $ann->created_at->format('M d, Y') }} · {{ $ann->target_audience }}</p>
                        </div>
                        <div class="flex items-center gap-2 ml-4 shrink-0">
                            <form action="{{ route('admin.announcements.toggle', $ann) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="text-xs px-2 py-1 rounded-lg {{ $ann->is_published ? 'bg-amber-500/10 text-amber-400' : 'bg-[#1392EC]/10 text-[#1392EC]' }} transition-all">
                                    {{ $ann->is_published ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.announcements.destroy', $ann) }}" method="POST" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-500 hover:text-red-400"><span class="material-symbols-outlined" style="font-size:16px;">delete</span></button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="px-5 py-3 border-t border-white/5">{{ $announcements->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
