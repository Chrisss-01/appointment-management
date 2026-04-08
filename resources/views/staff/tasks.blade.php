@extends('layouts.app')
@section('title', 'Clinic Tasks')
@section('page-title', 'Clinic Tasks')
@section('sidebar') @include('partials.sidebar-staff') @endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Add Task --}}
    <div class="lg:col-span-1">
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">New Task</h3>
            <form action="{{ route('staff.tasks.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Title</label>
                    <input type="text" name="title" required class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC]" placeholder="Task title...">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Description</label>
                    <textarea name="description" rows="2" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">Priority</label>
                        <select name="priority" class="w-full bg-[#141414] border border-white/10 rounded-xl px-3 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">Due Date</label>
                        <input type="date" name="due_date" class="w-full bg-[#141414] border border-white/10 rounded-xl px-3 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                    </div>
                </div>
                <button type="submit" class="w-full py-3 bg-[#1392EC] hover:opacity-90 text-white text-sm font-semibold rounded-xl transition-all">Add Task</button>
            </form>
        </div>
    </div>

    {{-- Task List --}}
    <div class="lg:col-span-2">
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/5">
                <h3 class="text-sm font-semibold text-white">All Tasks</h3>
            </div>
            @if($tasks->isEmpty())
            <div class="px-5 py-12 text-center">
                <span class="material-symbols-outlined text-gray-600 mb-3" style="font-size:48px;">task_alt</span>
                <p class="text-gray-400 text-sm">No tasks yet</p>
            </div>
            @else
            <div class="divide-y divide-white/5">
                @foreach($tasks as $task)
                <div class="px-5 py-4 hover:bg-white/[0.02] transition-colors">
                    <div class="flex items-center gap-4">
                        {{-- Status toggle --}}
                        <form action="{{ route('staff.tasks.status', $task) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $task->status === 'completed' ? 'pending' : 'completed' }}">
                            <button class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all
                                {{ $task->status === 'completed' ? 'border-[#1392EC] bg-[#1392EC] text-white' : 'border-gray-600 hover:border-[#1392EC]' }}">
                                @if($task->status === 'completed')
                                <span class="material-symbols-outlined" style="font-size:14px;">check</span>
                                @endif
                            </button>
                        </form>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium {{ $task->status === 'completed' ? 'text-gray-500 line-through' : 'text-white' }}">{{ $task->title }}</p>
                            @if($task->description)
                            <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($task->description, 80) }}</p>
                            @endif
                        </div>

                        @php $pc = ['low'=>'bg-gray-500/10 text-gray-400','medium'=>'bg-blue-500/10 text-blue-400','high'=>'bg-amber-500/10 text-amber-400','urgent'=>'bg-red-500/10 text-red-400']; @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $pc[$task->priority] ?? '' }}">{{ $task->priority }}</span>

                        @if($task->due_date)
                        <span class="text-xs {{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-red-400' : 'text-gray-500' }}">
                            {{ $task->due_date->format('M d') }}
                        </span>
                        @endif

                        <form action="{{ route('staff.tasks.destroy', $task) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmAction(this.form, 'Delete Task', 'Are you sure you want to delete this task?')" class="text-gray-600 hover:text-red-400 transition-colors">
                                <span class="material-symbols-outlined" style="font-size:16px;">close</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
