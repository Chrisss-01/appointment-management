@extends('layouts.app')

@section('title', 'Request ' . $certificateType->name)
@section('page-title', 'Certificate Request')
@section('sidebar')
    @include('partials.sidebar-student')
@endsection

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('student.certificates.request') }}" class="text-gray-400 hover:text-white transition-colors">
        <span class="material-symbols-outlined" style="font-size:20px;">arrow_back</span>
    </a>
    <div>
        <h2 class="text-lg font-bold text-white">{{ $certificateType->name }}</h2>
        <p class="text-sm text-gray-500">Fill in the form and upload required documents</p>
    </div>
</div>

<div class="max-w-2xl">
    <form action="{{ route('student.certificates.request.submit', $certificateType) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Purpose --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Purpose</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">Select Purpose <span class="text-red-400">*</span></label>
                    <select id="purpose-select" name="purpose_type" onchange="toggleOtherPurpose()" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-1 focus:ring-[#1392EC]" required>
                        <option value="">Select a purpose...</option>
                        @foreach($certificateType->purposePresets as $preset)
                            <option value="{{ $preset->label }}" {{ old('purpose_type') == $preset->label ? 'selected' : '' }}>{{ $preset->label }}</option>
                        @endforeach
                        <option value="other" {{ old('purpose_type') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('purpose_type')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="other-purpose-wrapper" class="hidden">
                    <label class="block text-xs text-gray-400 mb-1.5">Specify Purpose <span class="text-red-400">*</span></label>
                    <input type="text" id="other-purpose-input" name="purpose_text" value="{{ old('purpose_text') }}" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC]" placeholder="Enter your purpose...">
                    @error('purpose_text')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Required Documents --}}
        @if($certificateType->requiredDocuments->isNotEmpty())
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Required Documents</h3>

            <div class="space-y-4">
                @foreach($certificateType->requiredDocuments as $doc)
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5">
                        {{ $doc->name }}
                        @if($doc->is_required) <span class="text-red-400">*</span> @endif
                    </label>
                    @if($doc->description)
                        <p class="text-xs text-gray-600 mb-2">{{ $doc->description }}</p>
                    @endif
                    <input type="file" name="documents[{{ $doc->id }}]" {{ $doc->is_required ? 'required' : '' }} accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-400 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-[#1392EC]/10 file:text-[#1392EC] hover:file:bg-[#1392EC]/20 focus:outline-none focus:ring-1 focus:ring-[#1392EC]">
                    @error("documents.{$doc->id}")
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endforeach
            </div>

            <p class="text-xs text-gray-600 mt-3">Accepted formats: PDF, JPG, PNG (max 5MB each)</p>
        </div>
        @endif

        {{-- Additional Notes --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Additional Notes</h3>
            <textarea name="additional_notes" rows="3" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none" placeholder="Any additional information... (optional)">{{ old('additional_notes') }}</textarea>
        </div>

        {{-- Submit --}}
        <button id="submit-btn" type="submit" class="w-full py-3.5 bg-[#1392EC] hover:bg-[#1392EC]/90 text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-[#1392EC]/20 flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
            <span class="material-symbols-outlined" style="font-size:18px;">send</span>
            Submit Request
        </button>
    </form>
</div>

@push('scripts')
<script>
function toggleOtherPurpose() {
    const select = document.getElementById('purpose-select');
    const wrapper = document.getElementById('other-purpose-wrapper');
    const input = document.getElementById('other-purpose-input');

    if (select.value === 'other') {
        wrapper.classList.remove('hidden');
        input.setAttribute('required', 'required');
    } else {
        wrapper.classList.add('hidden');
        input.removeAttribute('required');
    }
}

// Init on page load
toggleOtherPurpose();

// Prevent double-submit
document.querySelector('form').addEventListener('submit', function () {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Submitting...';
});
</script>
@endpush
@endsection
