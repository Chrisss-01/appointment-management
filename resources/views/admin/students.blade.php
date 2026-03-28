@extends('layouts.app')
@section('title', 'Students')
@section('page-title', 'Student Management')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="mb-6">
    <form action="{{ route('admin.students') }}" method="GET" class="relative max-w-md">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-500" style="font-size:20px;">search</span>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search students..." class="w-full bg-[#141414] border border-white/10 rounded-xl pl-10 pr-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
    </form>
</div>

<div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
    @if($students->isEmpty())
    <div class="px-5 py-12 text-center"><p class="text-gray-400 text-sm">No students found</p></div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-gray-500 text-xs uppercase border-b border-white/5">
                    <th class="px-5 py-3 text-left font-medium">Student</th>
                    <th class="px-5 py-3 text-left font-medium">ID</th>
                    <th class="px-5 py-3 text-left font-medium">Program</th>
                    <th class="px-5 py-3 text-left font-medium">Year</th>
                    <th class="px-5 py-3 text-left font-medium">Status</th>
                    <th class="px-5 py-3 text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($students as $student)
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400 text-xs font-bold">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ $student->name }}</p>
                                <p class="text-xs text-gray-500">{{ $student->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-400 font-mono text-xs">{{ $student->student_id ?? '—' }}</td>
                    <td class="px-5 py-4 text-gray-400">{{ $student->program ?? '—' }}</td>
                    <td class="px-5 py-4 text-gray-400 capitalize text-xs">{{ str_replace('-',' ', $student->year_level ?? '—') }}</td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $student->is_active ? 'bg-[#1392EC]/10 text-[#1392EC]' : 'bg-red-500/10 text-red-400' }}">
                            {{ $student->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center gap-2 justify-end">
                            <form action="{{ route('admin.users.toggle-status', $student) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="text-xs px-2 py-1 rounded-lg {{ $student->is_active ? 'bg-red-500/10 text-red-400 hover:bg-red-500/20' : 'bg-[#1392EC]/10 text-[#1392EC] hover:bg-[#1392EC]/20' }} transition-all">
                                    {{ $student->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-white/5">{{ $students->links() }}</div>
    @endif
</div>
@endsection
