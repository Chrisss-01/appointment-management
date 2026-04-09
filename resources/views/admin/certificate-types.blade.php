@extends('layouts.app')
@section('title', 'Certificate Types')
@section('page-title', 'Certificate Type Management')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Add Type --}}
        <div class="lg:col-span-1">
            <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-4">Add Certificate Type</h3>
                <form action="{{ route('admin.certificate-types.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">Name</label>
                        <input type="text" name="name" required
                            class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]"
                            placeholder="e.g. Medical Certificate">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1.5">Description</label>
                        <textarea name="description" rows="2"
                            class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none"
                            placeholder="Short description of this certificate"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5">Color</label>
                            <input type="color" name="color" value="#1392EC"
                                class="w-full h-11 bg-[#141414] border border-white/10 rounded-xl px-2 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1.5">Icon</label>
                            <input type="text" name="icon" value="description"
                                class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]"
                                placeholder="Material icon name">
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full py-3 bg-[#1392EC] hover:bg-[#1392EC]/80 text-white text-sm font-semibold rounded-xl transition-all">Add
                        Certificate Type</button>
                </form>
            </div>
        </div>

        {{-- Type List --}}
        <div class="lg:col-span-2 space-y-4">
            @foreach($certificateTypes as $type)
                <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden" x-data="{ open: false }">
                    {{-- Type Header --}}
                    <div class="px-5 py-4 flex items-center gap-4 cursor-pointer" @click="open = !open">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                            style="background: {{ $type->color }}15;">
                            <span class="material-symbols-outlined"
                                style="font-size:20px; color: {{ $type->color }};">{{ $type->icon }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white">{{ $type->name }}</p>
                            <p class="text-xs text-gray-500">{{ $type->certificate_requests_count }} requests ·
                                {{ $type->requiredDocuments->count() }} docs · {{ $type->purposePresets->count() }} purposes
                            </p>
                        </div>
                        <span
                            class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $type->is_active ? 'bg-[#1392EC]/10 text-[#1392EC]' : 'bg-red-500/10 text-red-400' }}">
                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="material-symbols-outlined text-gray-500 transition-transform" :class="open && 'rotate-180'"
                            style="font-size:18px;">expand_more</span>
                    </div>

                    {{-- Expandable Detail --}}
                    <div x-show="open" x-cloak class="border-t border-white/5">
                        {{-- Edit Type --}}
                        <div class="px-5 py-4 border-b border-white/5">
                            <form action="{{ route('admin.certificate-types.update', $type) }}" method="POST"
                                class="flex flex-wrap gap-3 items-end">
                                @csrf @method('PUT')
                                <div class="flex-1 min-w-[120px]">
                                    <label class="block text-xs text-gray-500 mb-1">Name</label>
                                    <input type="text" name="name" value="{{ $type->name }}" required
                                        class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                                </div>
                                <div class="flex-1 min-w-[120px]">
                                    <label class="block text-xs text-gray-500 mb-1">Description</label>
                                    <input type="text" name="description" value="{{ $type->description }}"
                                        class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                                </div>
                                <div class="w-14">
                                    <label class="block text-xs text-gray-500 mb-1">Color</label>
                                    <input type="color" name="color" value="{{ $type->color }}"
                                        class="w-full h-9 bg-[#141414] border border-white/10 rounded-lg px-1 cursor-pointer">
                                </div>
                                <div class="w-24">
                                    <label class="block text-xs text-gray-500 mb-1">Icon</label>
                                    <input type="text" name="icon" value="{{ $type->icon }}"
                                        class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                                </div>
                                <label class="flex items-center gap-2 text-xs text-gray-400">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" {{ $type->is_active ? 'checked' : '' }}
                                        class="rounded bg-[#141414] border-white/10 text-[#1392EC] focus:ring-[#1392EC]">
                                    Active
                                </label>
                                <button type="submit"
                                    class="px-4 py-2 bg-[#1392EC]/10 text-[#1392EC] text-xs font-medium rounded-lg hover:bg-[#1392EC]/20 transition-all">Update</button>
                                @if(!$type->certificate_requests_count)
                                    </form>
                                    <form action="{{ route('admin.certificate-types.destroy', $type) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Delete this certificate type?')">
                                        @csrf @method('DELETE')
                                        <button
                                            class="px-4 py-2 bg-red-500/10 text-red-400 text-xs font-medium rounded-lg hover:bg-red-500/20 transition-all">Delete</button>
                                    </form>
                                @else
                                </form>
                            @endif
                        </div>

                        {{-- Required Documents --}}
                        <div class="px-5 py-4 border-b border-white/5">
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Required Documents
                            </h4>
                            <div class="space-y-2 mb-3" id="doc-list-{{ $type->id }}">
                                @foreach($type->requiredDocuments as $doc)
                                    <div class="flex items-center justify-between px-3 py-2 bg-[#141414] rounded-lg">
                                        <div>
                                            <span class="text-sm text-white">{{ $doc->name }}</span>
                                            @if($doc->description)
                                                <span class="text-xs text-gray-500 ml-2">{{ $doc->description }}</span>
                                            @endif
                                            @if($doc->is_required)
                                                <span class="text-[10px] text-amber-400 ml-1">Required</span>
                                            @else
                                                <span class="text-[10px] text-gray-500 ml-1">Optional</span>
                                            @endif
                                        </div>
                                        <button type="button"
                                            data-delete-btn
                                            data-url="{{ route('admin.certificate-types.documents.destroy', $doc) }}"
                                            data-csrf="{{ csrf_token() }}"
                                            data-confirm="Remove this document requirement?"
                                            class="text-gray-500 hover:text-red-400 transition-colors">
                                            <span class="material-symbols-outlined" style="font-size:14px;">close</span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <form data-doc-form
                                  data-url="{{ route('admin.certificate-types.documents.store', $type) }}"
                                  data-delete-base="{{ url('admin/certificate-type-documents') }}"
                                  data-csrf="{{ csrf_token() }}"
                                  data-list="doc-list-{{ $type->id }}"
                                  class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <input type="text" name="name" required
                                        class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]"
                                        placeholder="Document name">
                                </div>
                                <div class="flex-1">
                                    <input type="text" name="description"
                                        class="w-full bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC]"
                                        placeholder="Description (opt)">
                                </div>
                                <label class="flex items-center gap-1 text-xs text-gray-400 shrink-0">
                                    <input type="checkbox" name="is_required" value="1" checked
                                        class="rounded bg-[#141414] border-white/10 text-[#1392EC] focus:ring-[#1392EC]">
                                    Req
                                </label>
                                <button type="submit"
                                    class="px-3 py-2 bg-[#1392EC]/10 text-[#1392EC] text-xs font-medium rounded-lg hover:bg-[#1392EC]/20 transition-all shrink-0 disabled:opacity-40 disabled:cursor-not-allowed">
                                    <span class="material-symbols-outlined" style="font-size:14px;">add</span>
                                </button>
                            </form>
                        </div>

                        {{-- Purpose Presets --}}
                        <div class="px-5 py-4">
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Purpose Presets</h4>
                            <div class="flex flex-wrap gap-2 mb-3" id="purpose-list-{{ $type->id }}">
                                @foreach($type->purposePresets as $purpose)
                                    <div
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-[#141414] border border-white/5 rounded-lg">
                                        <span class="text-sm text-white">{{ $purpose->label }}</span>
                                        <button type="button"
                                            data-delete-btn
                                            data-url="{{ route('admin.certificate-types.purposes.destroy', $purpose) }}"
                                            data-csrf="{{ csrf_token() }}"
                                            data-confirm="Remove this purpose preset?"
                                            class="text-gray-500 hover:text-red-400 transition-colors">
                                            <span class="material-symbols-outlined" style="font-size:12px;">close</span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <form data-purpose-form
                                  data-url="{{ route('admin.certificate-types.purposes.store', $type) }}"
                                  data-delete-base="{{ url('admin/certificate-purpose-presets') }}"
                                  data-csrf="{{ csrf_token() }}"
                                  data-list="purpose-list-{{ $type->id }}"
                                  class="flex gap-2">
                                <input type="text" name="label" required
                                    class="flex-1 bg-[#141414] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]"
                                    placeholder="Add a purpose preset">
                                <button type="submit"
                                    class="px-3 py-2 bg-[#1392EC]/10 text-[#1392EC] text-xs font-medium rounded-lg hover:bg-[#1392EC]/20 transition-all shrink-0 disabled:opacity-40 disabled:cursor-not-allowed">
                                    <span class="material-symbols-outlined" style="font-size:14px;">add</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($certificateTypes->isEmpty())
                <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl px-5 py-12 text-center">
                    <span class="material-symbols-outlined text-gray-600 mb-2" style="font-size:40px;">description</span>
                    <p class="text-sm text-gray-500">No certificate types yet</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Purpose Presets: inline add via fetch ──────────────────────────
    document.querySelectorAll('[data-purpose-form]').forEach(function (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn        = form.querySelector('[type="submit"]');
            const input      = form.querySelector('[name="label"]');
            const label      = input.value.trim();
            const url        = form.dataset.url;
            const deleteBase = form.dataset.deleteBase;
            const listId     = form.dataset.list;
            const csrf       = form.dataset.csrf;

            if (!label || btn.disabled) return;
            btn.disabled = true;

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf || CSRF,
                    },
                    body: JSON.stringify({ label }),
                });

                if (!res.ok) throw new Error('Server error');

                const preset     = await res.json();
                const list       = document.getElementById(listId);
                const deleteUrl  = deleteBase + '/' + preset.id;

                const deleteBtn  = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.setAttribute('data-delete-btn', '');
                deleteBtn.setAttribute('data-url', deleteUrl);
                deleteBtn.setAttribute('data-csrf', csrf || CSRF);
                deleteBtn.setAttribute('data-confirm', 'Remove this purpose preset?');
                deleteBtn.className = 'text-gray-500 hover:text-red-400 transition-colors';
                deleteBtn.innerHTML = '<span class="material-symbols-outlined" style="font-size:12px;">close</span>';

                const chip = document.createElement('div');
                chip.className = 'flex items-center gap-1.5 px-3 py-1.5 bg-[#141414] border border-white/5 rounded-lg';
                chip.innerHTML = `<span class="text-sm text-white">${preset.label}</span>`;
                chip.appendChild(deleteBtn);

                if (list) list.appendChild(chip);
                input.value = '';
                if (window.Notify) Notify.success('Purpose preset added.');
            } catch (err) {
                console.error('Failed to add purpose preset:', err);
            } finally {
                btn.disabled = false;
            }
        });
    });

    // ── Required Documents: inline add via fetch ───────────────────────
    document.querySelectorAll('[data-doc-form]').forEach(function (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn         = form.querySelector('[type="submit"]');
            const nameInput   = form.querySelector('[name="name"]');
            const descInput   = form.querySelector('[name="description"]');
            const reqCheckbox = form.querySelector('[name="is_required"]');
            const name        = nameInput.value.trim();
            const description = descInput?.value.trim() ?? '';
            const is_required = reqCheckbox ? reqCheckbox.checked : true;
            const url         = form.dataset.url;
            const deleteBase  = form.dataset.deleteBase;
            const listId      = form.dataset.list;
            const csrf        = form.dataset.csrf;

            if (!name || btn.disabled) return;
            btn.disabled = true;

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf || CSRF,
                    },
                    body: JSON.stringify({ name, description, is_required }),
                });

                if (!res.ok) throw new Error('Server error');

                const doc        = await res.json();
                const list       = document.getElementById(listId);
                const deleteUrl  = deleteBase + '/' + doc.id;

                const deleteBtn  = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.setAttribute('data-delete-btn', '');
                deleteBtn.setAttribute('data-url', deleteUrl);
                deleteBtn.setAttribute('data-csrf', csrf || CSRF);
                deleteBtn.setAttribute('data-confirm', 'Remove this document requirement?');
                deleteBtn.className = 'text-gray-500 hover:text-red-400 transition-colors';
                deleteBtn.innerHTML = '<span class="material-symbols-outlined" style="font-size:14px;">close</span>';

                const badge      = doc.is_required
                    ? '<span class="text-[10px] text-amber-400 ml-1">Required</span>'
                    : '<span class="text-[10px] text-gray-500 ml-1">Optional</span>';
                const descBadge  = doc.description
                    ? `<span class="text-xs text-gray-500 ml-2">${doc.description}</span>`
                    : '';

                const row = document.createElement('div');
                row.className = 'flex items-center justify-between px-3 py-2 bg-[#141414] rounded-lg';
                row.innerHTML = `
                    <div>
                        <span class="text-sm text-white">${doc.name}</span>
                        ${descBadge}
                        ${badge}
                    </div>`;
                row.appendChild(deleteBtn);

                if (list) list.appendChild(row);

                nameInput.value = '';
                if (descInput) descInput.value = '';
                if (window.Notify) Notify.success('Document requirement added.');
            } catch (err) {
                console.error('Failed to add document:', err);
            } finally {
                btn.disabled = false;
            }
        });
    });
    // ── Shared AJAX delete via event delegation ────────────────────────
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('[data-delete-btn]');
        if (!btn) return;

        const url     = btn.dataset.url;
        const csrf    = btn.dataset.csrf || CSRF;
        const message = btn.dataset.confirm || 'Are you sure?';
        if (!url) return;

        let confirmed = false;
        if (window.Notify?.confirm) {
            const result = await Notify.confirm('Delete Item', message, 'Remove');
            confirmed = result.isConfirmed;
        } else {
            confirmed = window.confirm(message);
        }
        if (!confirmed) return;

        btn.disabled = true;
        try {
            const res = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
            });

            if (!res.ok) throw new Error('Delete failed');

            const row = btn.closest('[class*="bg-\\[#141414\\]"]') ?? btn.closest('div');
            if (row) {
                row.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(6px)';
                setTimeout(() => row.remove(), 210);
            }
            if (window.Notify) Notify.success('Item removed.');
        } catch (err) {
            console.error('Delete failed:', err);
            btn.disabled = false;
        }
    });
}());
</script>
@endpush