@extends('layouts.app')
@section('title', 'Announcements')
@section('page-title', 'Announcements')
@section('sidebar') @include('partials.sidebar-staff') @endsection

@section('content')

{{-- Flash messages --}}
@if(session('success'))
<div class="mb-4 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="announcementManager()">

    {{-- Create Form --}}
    <div class="lg:col-span-1">
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">New Announcement</h3>
            <form action="{{ route('staff.announcements.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Title</label>
                    <input type="text" name="title" required
                           class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Content</label>
                    <textarea name="content" rows="4" required
                              class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none"></textarea>
                </div>
                {{-- Target audience is always Students for staff --}}
                <div class="flex items-center gap-2 px-4 py-3 bg-[#141414] border border-white/10 rounded-xl">
                    <span class="text-sm text-gray-400">Target audience: <span class="text-[#1392EC] font-medium">Students</span></span>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Expires At (optional)</label>
                    <input type="date" name="expires_at"
                           class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" checked
                           class="w-4 h-4 rounded bg-[#141414] text-[#1392EC] border-white/10 focus:ring-[#1392EC]">
                    <span class="text-sm text-gray-400">Publish immediately</span>
                </label>
                <button type="submit"
                        class="w-full py-3 bg-[#1392EC] hover:bg-[#1392EC]/90 text-white text-sm font-semibold rounded-xl transition-all">
                    Post Announcement
                </button>
            </form>
        </div>
    </div>

    {{-- Announcements List --}}
    <div class="lg:col-span-2">
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/5">
                <h3 class="text-sm font-semibold text-white">All Announcements</h3>
            </div>
            @if($announcements->isEmpty())
            <div class="px-5 py-12 text-center">
                <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">campaign</span>
                <p class="text-gray-400 text-sm">No announcements yet</p>
            </div>
            @else
            <div class="divide-y divide-white/5">
                @foreach($announcements as $ann)
                <div class="px-5 py-4 hover:bg-white/[0.02] transition-colors">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                {{-- Author source badge --}}
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase bg-[#1392EC]/10 text-[#1392EC]">
                                    {{ $ann->author->role === 'admin' ? 'Admin' : 'Staff' }}
                                </span>
                                {{-- Draft badge --}}
                                @if(!$ann->is_published)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase bg-[#1392EC]/10 text-[#1392EC]">Draft</span>
                                @endif
                                <h4 class="text-sm font-medium text-white">{{ $ann->title }}</h4>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">
                                By {{ $ann->author->name }} ·
                                {{ $ann->published_at ? $ann->published_at->diffForHumans() : $ann->created_at->diffForHumans() }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1.5 line-clamp-2">{{ Str::limit($ann->content, 120) }}</p>
                        </div>

                        {{-- Actions: only for own staff-authored announcements --}}
                        @if($ann->author_id === auth()->id())
                        <div class="flex items-center gap-2 shrink-0">
                            {{-- Edit button --}}
                            <button
                                @click="openEdit({{ $ann->id }}, '{{ addslashes($ann->title) }}', '{{ addslashes($ann->content) }}', '{{ $ann->expires_at?->format('Y-m-d') ?? '' }}')"
                                class="text-gray-500 hover:text-[#1392EC] transition-colors">
                                <span class="material-symbols-outlined" style="font-size:16px;">edit</span>
                            </button>
                            {{-- Toggle publish --}}
                            <form action="{{ route('staff.announcements.toggle', $ann) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="text-xs px-2 py-1 rounded-lg {{ $ann->is_published ? 'bg-amber-500/10 text-amber-400' : 'bg-[#1392EC]/10 text-[#1392EC]' }} transition-all">
                                    {{ $ann->is_published ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>
                            {{-- Delete --}}
                            <form action="{{ route('staff.announcements.destroy', $ann) }}" method="POST"
                                  onsubmit="return confirm('Delete this announcement?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-500 hover:text-red-400 transition-colors">
                                    <span class="material-symbols-outlined" style="font-size:16px;">delete</span>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div class="px-5 py-3 border-t border-white/5">{{ $announcements->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editing" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
         @keydown.escape.window="editing = false">
        <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 w-full max-w-lg mx-4" @click.stop>
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-semibold text-white">Edit Announcement</h3>
                <button @click="editing = false" class="text-gray-500 hover:text-white">
                    <span class="material-symbols-outlined" style="font-size:20px;">close</span>
                </button>
            </div>
            <form :action="editUrl" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Title</label>
                    <input type="text" name="title" x-model="form.title" required
                           class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Content</label>
                    <textarea name="content" rows="4" x-model="form.content" required
                              class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none"></textarea>
                </div>
                {{-- Target audience is always students for staff --}}
                <div class="flex items-center gap-2 px-4 py-3 bg-[#141414] border border-white/10 rounded-xl">
                    <span class="text-sm text-gray-400">Target audience: <span class="text-[#1392EC] font-medium">Students</span></span>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Expires At (optional)</label>
                    <input type="date" name="expires_at" x-model="form.expires_at"
                           class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" @click="editing = false"
                            class="flex-1 py-3 bg-transparent border border-white/10 text-gray-400 text-sm font-medium rounded-xl hover:border-white/20 transition-all">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 bg-[#1392EC] hover:bg-[#1392EC]/90 text-white text-sm font-semibold rounded-xl transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function announcementManager() {
    return {
        editing: false,
        editUrl: '',
        form: { title: '', content: '', expires_at: '' },
        openEdit(id, title, content, expires) {
            this.editUrl = `/staff/announcements/${id}`;
            this.form.title = title;
            this.form.content = content;
            this.form.expires_at = expires;
            this.editing = true;
        }
    };
}
</script>
@endsection
