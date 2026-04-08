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

@if($activeRequest)
<div class="bg-[#1A1A1A] border border-orange-500/20 rounded-2xl p-8 text-center max-w-2xl mt-4">
    <div class="w-16 h-16 bg-orange-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
        <span class="material-symbols-outlined text-orange-400" style="font-size:32px;">pending_actions</span>
    </div>
    <h3 class="text-xl font-semibold text-white mb-2">Active Request Exists</h3>
    <p class="text-gray-400 text-sm mb-6 max-w-md mx-auto">
        You already have a request for a <strong>{{ $certificateType->name }}</strong> currently marked as <span class="text-orange-400 font-medium">{{ ucwords(str_replace('_', ' ', $activeRequest->status)) }}</span>. 
        Please wait for this request to be processed before submitting a new one for the same certificate.
    </p>
    <a href="{{ route('student.certificates.my') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white/5 hover:bg-white/10 text-white text-sm font-medium rounded-xl transition-colors border border-white/10">
        View My Certificates
        <span class="material-symbols-outlined" style="font-size:18px;">arrow_forward</span>
    </a>
</div>
@else
<div class="max-w-2xl">
    <form id="certificate-request-form" action="{{ route('student.certificates.request.submit', $certificateType) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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

        {{-- Medical History --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-1">Medical History</h3>
            <p class="text-xs text-gray-500 mb-4">Please provide any relevant past illnesses, surgeries, or conditions.</p>
            <textarea name="medical_history" rows="3" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none" placeholder="Your medical history... (optional)">{{ old('medical_history') }}</textarea>
            @error('medical_history')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Additional Notes --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Additional Notes</h3>
            <textarea name="additional_notes" rows="3" class="w-full bg-[#141414] border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#1392EC] resize-none" placeholder="Any additional information... (optional)">{{ old('additional_notes') }}</textarea>
            @error('additional_notes')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button id="submit-btn" type="submit" class="w-full py-3.5 bg-[#1392EC] hover:bg-[#1392EC]/90 text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-[#1392EC]/20 flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
            <span class="material-symbols-outlined" style="font-size:18px;">send</span>
            Submit Request
        </button>
    </form>
</div>
@endif

@push('scripts')
@if(!$activeRequest)
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

// Handle Form Submission via AJAX
const requestForm = document.getElementById('certificate-request-form');
requestForm.addEventListener('submit', async function (e) {
    e.preventDefault();
    
    const form = this;
    const btn = document.getElementById('submit-btn');
    const originalContent = btn.innerHTML;
    
    // Enter Loading State
    btn.disabled = true;
    btn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Processing...';
    
    // Clear previous errors
    form.querySelectorAll('.text-red-400.mt-1').forEach(el => el.remove());
    form.querySelectorAll('.border-red-400').forEach(el => el.classList.remove('border-red-400'));

    try {
        const formData = new FormData(form);
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (response.ok && result.success) {
            // Success Logic
            Notify.success(result.message);
            
            // Reset Form
            form.reset();
            
            // Reset "Other" purpose if needed
            toggleOtherPurpose();
            
            // Scroll to top of form
            form.scrollIntoView({ behavior: 'smooth' });
        } else {
            // Validation or Server Errors
            if (response.status === 422) {
                // Handle Laravel Validation Errors
                const errors = result.errors;
                Object.keys(errors).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`) || form.querySelector(`[name="${key.split('.')[0]}[${key.split('.')[1]}]"]`);
                    if (input) {
                        input.classList.add('border-red-400');
                        const errorMsg = document.createElement('p');
                        errorMsg.className = 'text-xs text-red-400 mt-1';
                        errorMsg.textContent = errors[key][0];
                        input.parentNode.appendChild(errorMsg);
                    }
                });
                Notify.error('Validation Error', 'Please check the form for errors.');
            } else {
                Notify.error('Error', result.message || 'Something went wrong. Please try again.');
            }
        }
    } catch (error) {
        console.error('Submission Error:', error);
        Notify.error('Submission Failed', 'A network error occurred. Please check your connection.');
    } finally {
        // Exit Loading State
        btn.disabled = false;
        btn.innerHTML = originalContent;
    }
});
</script>
@endif
@endpush
@endsection
