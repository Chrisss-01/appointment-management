@extends('layouts.app')

@section('title', 'Certificate Request Detail')
@section('page-title', 'Certificate Request Detail')
@section('sidebar')
    @include('partials.sidebar-staff')
@endsection

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('staff.certificate-requests') }}" class="text-gray-400 hover:text-white transition-colors">
        <span class="material-symbols-outlined" style="font-size:20px;">arrow_back</span>
    </a>
    <div>
        <h2 class="text-lg font-bold text-white">{{ $certificateRequest->certificateType->name }}</h2>
        <p class="text-sm text-gray-500">Request from {{ $certificateRequest->student->name }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Request Details --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Student Info --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Student Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Name</p>
                    <p class="text-sm text-white font-medium">{{ $certificateRequest->student->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Student ID</p>
                    <p class="text-sm text-white font-medium">{{ $certificateRequest->student->student_id ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Email</p>
                    <p class="text-sm text-white">{{ $certificateRequest->student->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Department</p>
                    <p class="text-sm text-white">{{ $certificateRequest->student->department ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Request Info --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Request Details</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Certificate Type</p>
                    <p class="text-sm text-white font-medium">{{ $certificateRequest->certificateType->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Purpose</p>
                    <p class="text-sm text-white">
                        @if($certificateRequest->purpose_type === 'other')
                            {{ $certificateRequest->purpose_text ?? 'Other (unspecified)' }}
                        @else
                            {{ $certificateRequest->purpose_type ?? 'Not specified' }}
                        @endif
                    </p>
                </div>
                @if($certificateRequest->additional_notes)
                <div>
                    <p class="text-xs text-gray-500 mb-1">Additional Notes</p>
                    <p class="text-sm text-white">{{ $certificateRequest->additional_notes }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-gray-500 mb-1">Submitted</p>
                    <p class="text-sm text-white">{{ $certificateRequest->created_at->format('M d, Y g:i A') }}</p>
                </div>
            </div>
        </div>

        {{-- Uploaded Documents --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Uploaded Documents</h3>
            @if($certificateRequest->documents->isEmpty())
                <p class="text-sm text-gray-500">No documents uploaded</p>
            @else
                <div class="space-y-3">
                    @foreach($certificateRequest->documents as $doc)
                    <div class="flex items-center justify-between px-4 py-3 bg-[#141414] border border-white/5 rounded-xl">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[#1392EC]" style="font-size:20px;">
                                {{ str_contains($doc->mime_type ?? '', 'pdf') ? 'picture_as_pdf' : 'image' }}
                            </span>
                            <div>
                                <p class="text-sm text-white">{{ $doc->typeDocument->name ?? 'Document' }}</p>
                                <p class="text-xs text-gray-500">{{ $doc->original_name }} · {{ number_format(($doc->file_size ?? 0) / 1024, 1) }} KB</p>
                            </div>
                        </div>
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-[#1392EC] hover:text-[#1392EC]/80 transition-colors">
                            <span class="material-symbols-outlined" style="font-size:18px;">open_in_new</span>
                        </a>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Medical History --}}
        @php $medicalRecords = $certificateRequest->student->medicalRecords; @endphp
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl overflow-hidden">
            <button type="button" id="med-history-toggle" class="w-full flex items-center justify-between px-6 py-4 text-left group">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#1392EC]" style="font-size:18px;">history</span>
                    <h3 class="text-sm font-semibold text-white">Medical History</h3>
                    @if($medicalRecords->isNotEmpty())
                    <span class="px-2 py-0.5 text-[10px] font-medium bg-[#1392EC]/10 text-[#1392EC] border border-[#1392EC]/20 rounded-full">{{ $medicalRecords->count() }}</span>
                    @endif
                </div>
                <span id="med-history-chevron" class="material-symbols-outlined text-gray-500 transition-transform duration-200" style="font-size:18px;">expand_more</span>
            </button>

            <div id="med-history-body" class="hidden">
                <div class="px-6 pb-2 flex items-center justify-between border-t border-white/5 pt-3">
                    <p class="text-xs text-gray-500">Showing all records for this student</p>
                    <a href="{{ route('staff.patients.show', $certificateRequest->student) }}" class="text-xs text-[#1392EC] hover:underline flex items-center gap-1">
                        View Full Profile
                        <span class="material-symbols-outlined" style="font-size:13px;">open_in_new</span>
                    </a>
                </div>

                @if($medicalRecords->isEmpty())
                <div class="px-6 py-8 text-center text-gray-500 text-sm">No medical records on file for this student.</div>
                @else
                <div class="divide-y divide-white/5 max-h-96 overflow-y-auto custom-scrollbar">
                    @foreach($medicalRecords as $record)
                    @php
                        $serviceColor = $record->appointment?->service?->color;
                        $fallbackColor = match($record->record_type) {
                            'consultation' => '#1392EC',
                            'dental'       => '#3B82F6',
                            default        => '#F59E0B',
                        };
                        $finalColor = $serviceColor ?? $fallbackColor;
                    @endphp
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium" style="color: {{ $finalColor }};">
                                {{ $record->service_name ?? (ucfirst($record->record_type) . ' Record') }}
                            </span>
                            <span class="text-[10px] text-gray-600">{{ $record->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($record->chief_complaint)
                        <p class="text-sm text-gray-300"><span class="text-gray-500">Complaint:</span> {{ $record->chief_complaint }}</p>
                        @endif
                        @if($record->diagnosis)
                        <p class="text-sm text-gray-300 mt-1"><span class="text-gray-500">Diagnosis:</span> {{ $record->diagnosis }}</p>
                        @endif
                        @if($record->treatment)
                        <p class="text-sm text-gray-300 mt-1"><span class="text-gray-500">Treatment:</span> {{ $record->treatment }}</p>
                        @endif
                        @if($record->prescription)
                        <p class="text-sm text-gray-300 mt-1"><span class="text-gray-500">Prescription:</span> {{ $record->prescription }}</p>
                        @endif

                        @if($record->vital_signs)
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($record->vital_signs as $key => $val)
                            @if($val)
                            <span class="px-2 py-1 bg-white/5 rounded text-[10px] text-gray-400">
                                <span class="text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> {{ $val }}
                            </span>
                            @endif
                            @endforeach
                        </div>
                        @endif

                        @if($record->visual_acuity)
                        @php
                            $va = $record->visual_acuity;
                            $vaLabels = [
                                'wears_correction' => 'Wears Correction',
                                'od'               => 'OD',
                                'os'               => 'OS',
                                'color_vision'     => 'Color Vision',
                                'recommendation'   => 'Recommendation',
                            ];
                            $vaFormats = [
                                'wears_correction' => fn($v) => ucfirst($v),
                                'color_vision'     => fn($v) => ucfirst($v),
                                'recommendation'   => fn($v) => ucwords(str_replace('_', ' ', $v)),
                            ];
                        @endphp
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($vaLabels as $key => $label)
                            @if(!empty($va[$key]))
                            <span class="px-2 py-1 bg-white/5 rounded text-[10px] text-gray-400">
                                <span class="text-gray-500">{{ $label }}:</span>
                                {{ isset($vaFormats[$key]) ? $vaFormats[$key]($va[$key]) : $va[$key] }}
                            </span>
                            @endif
                            @endforeach
                        </div>
                        @endif

                        @if($record->notes)
                        <p class="text-sm text-gray-300 mt-1"><span class="text-gray-500">Notes:</span> {{ $record->notes }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Actions Panel --}}
    <div class="space-y-6">
        {{-- Status Card --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Status</h3>
            @php
                $statusColors = [
                    'pending' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                    'documents_verified' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                    'approved' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                    'rejected' => 'bg-red-500/10 text-red-400 border-red-500/20',
                ];
                $statusLabels = [
                    'pending' => 'Pending Review',
                    'documents_verified' => 'Documents Verified',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ];
            @endphp
            <div class="px-4 py-3 rounded-xl border {{ $statusColors[$certificateRequest->status] ?? '' }}">
                <p class="text-sm font-semibold">{{ $statusLabels[$certificateRequest->status] ?? $certificateRequest->status }}</p>
            </div>

            @if($certificateRequest->verified_at)
                <p class="text-xs text-gray-500 mt-3">
                    <span class="text-blue-400">Verified</span> by {{ $certificateRequest->verifiedByUser->name ?? 'Staff' }}
                    on {{ $certificateRequest->verified_at->format('M d, Y') }}
                </p>
            @endif
            @if($certificateRequest->approved_at)
                <p class="text-xs text-gray-500 mt-1">
                    <span class="text-emerald-400">Approved</span> by {{ $certificateRequest->approvedByUser->name ?? 'Doctor' }}
                    on {{ $certificateRequest->approved_at->format('M d, Y') }}
                </p>
            @endif
            @if($certificateRequest->certificate_number)
                <p class="text-xs text-gray-500 mt-1 font-mono">Cert #: {{ $certificateRequest->certificate_number }}</p>
            @endif
        </div>

        {{-- Actions --}}
        @if(!$certificateRequest->isApproved() && !$certificateRequest->isRejected())
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Actions</h3>
            <div class="space-y-3">
                {{-- Verify Documents (nurse can do this on pending) --}}
                @if($certificateRequest->isPending() && auth()->user()->isNurse())
                <form action="{{ route('staff.certificate-requests.verify', $certificateRequest) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-full py-2.5 bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500/20 text-sm font-medium rounded-xl transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:16px;">verified</span>
                        Mark Documents Verified
                    </button>
                </form>
                @endif

                @if(auth()->user()->isDoctor())
                <form action="{{ route('staff.certificate-requests.approve', $certificateRequest) }}" method="POST" id="approve-form">
                    @csrf @method('PATCH')
                    <button type="button" id="approve-btn" class="w-full py-2.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 text-sm font-medium rounded-xl transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:16px;">check_circle</span>
                        Approve & Generate Certificate
                    </button>
                </form>
                @endif

                {{-- Reject (both nurse and doctor) --}}
                <form action="{{ route('staff.certificate-requests.reject', $certificateRequest) }}" method="POST" id="reject-form">
                    @csrf @method('PATCH')
                    <div id="reject-reason-wrapper" class="hidden mb-3">
                        <textarea name="rejection_reason" rows="2" class="w-full bg-[#141414] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-red-500 resize-none" placeholder="Reason for rejection..." required></textarea>
                    </div>
                    <button type="button" id="reject-btn" class="w-full py-2.5 bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 text-sm font-medium rounded-xl transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:16px;">cancel</span>
                        Reject Request
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($certificateRequest->isRejected() && $certificateRequest->rejection_reason)
        <div class="bg-[#1A1A1A] border border-red-500/10 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-red-400 mb-2">Rejection Reason</h3>
            <p class="text-sm text-gray-400">{{ $certificateRequest->rejection_reason }}</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Medical history toggle
const medHistoryToggle = document.getElementById('med-history-toggle');
const medHistoryBody = document.getElementById('med-history-body');
const medHistoryChevron = document.getElementById('med-history-chevron');

medHistoryToggle?.addEventListener('click', () => {
    const isHidden = medHistoryBody.classList.contains('hidden');
    medHistoryBody.classList.toggle('hidden', !isHidden);
    medHistoryChevron.style.transform = isHidden ? 'rotate(180deg)' : '';
});

// Approve confirmation
document.getElementById('approve-btn')?.addEventListener('click', () => {
    Notify.confirm('Approve Certificate', 'Approve this certificate and generate PDF?').then(res => {
        if(res.isConfirmed) document.getElementById('approve-form').submit();
    });
});

// Reject button toggle
const rejectBtn = document.getElementById('reject-btn');
const rejectWrapper = document.getElementById('reject-reason-wrapper');
const rejectForm = document.getElementById('reject-form');
let rejectVisible = false;

rejectBtn?.addEventListener('click', () => {
    if (!rejectVisible) {
        rejectWrapper.classList.remove('hidden');
        rejectVisible = true;
        rejectBtn.textContent = 'Confirm Rejection';
    } else {
        rejectForm.submit();
    }
});
</script>
@endpush
@endsection
