<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CertificateRequest;
use App\Models\CertificateType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    /**
     * Certificate request page — show certificate types as cards.
     */
    public function requestIndex()
    {
        $certificateTypes = CertificateType::active()
            ->withCount('requiredDocuments')
            ->get();

        return view('student.certificate-request', compact('certificateTypes'));
    }

    /**
     * Show the request form for a specific certificate type.
     */
    public function requestForm(CertificateType $certificateType)
    {
        $activeRequest = \App\Models\CertificateRequest::where('student_id', request()->user()->id)
            ->where('certificate_type_id', $certificateType->id)
            ->whereIn('status', ['pending', 'documents_verified'])
            ->first();

        $certificateType->load(['requiredDocuments', 'purposePresets']);

        return view('student.certificate-request-form', compact('certificateType', 'activeRequest'));
    }

    /**
     * Submit a certificate request.
     */
    public function submitRequest(Request $request, CertificateType $certificateType)
    {
        $certificateType->load('requiredDocuments');

        $rules = [
            'purpose_type' => 'required|string|max:500',
            'purpose_text' => 'nullable|string|max:500|required_if:purpose_type,other',
            'medical_history' => 'nullable|string|max:1000',
            'additional_notes' => 'nullable|string|max:1000',
        ];

        // Validate required documents
        foreach ($certificateType->requiredDocuments as $doc) {
            $key = "documents.{$doc->id}";
            if ($doc->is_required) {
                $rules[$key] = 'required|file|max:5120|mimes:pdf,jpg,jpeg,png';
            } else {
                $rules[$key] = 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png';
            }
        }

        $validated = $request->validate($rules);

        // Active request guard: 1 active per type
        $hasActiveRequest = CertificateRequest::where('student_id', $request->user()->id)
            ->where('certificate_type_id', $certificateType->id)
            ->whereIn('status', ['pending', 'documents_verified'])
            ->exists();

        if ($hasActiveRequest) {
            return back()
                ->withInput()
                ->withErrors(['purpose_type' => 'You already have an active request for this certificate type.']);
        }

        // Duplicate-submission guard: same student, same type, same purpose within 30 seconds
        $isDuplicate = CertificateRequest::where('student_id', $request->user()->id)
            ->where('certificate_type_id', $certificateType->id)
            ->where('purpose_type', $validated['purpose_type'])
            ->where('created_at', '>=', now()->subSeconds(30))
            ->exists();

        if ($isDuplicate) {
            return back()
                ->withInput()
                ->withErrors(['purpose_type' => 'You just submitted an identical request. Please wait a moment before trying again.']);
        }

        // Create certificate request
        $certRequest = CertificateRequest::create([
            'student_id' => $request->user()->id,
            'certificate_type_id' => $certificateType->id,
            'purpose_type' => $validated['purpose_type'],
            'purpose_text' => $validated['purpose_type'] === 'other' ? ($validated['purpose_text'] ?? null) : null,
            'medical_history' => $validated['medical_history'] ?? null,
            'additional_notes' => $validated['additional_notes'] ?? null,
            'status' => 'pending',
        ]);

        // Upload documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $docId => $file) {
                $path = $file->store("certificate-documents/{$certRequest->id}", 'public');
                $certRequest->documents()->create([
                    'certificate_type_document_id' => $docId,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Certificate request submitted successfully!',
                'redirect_url' => route('student.certificates.my')
            ]);
        }
    
        return redirect()->route('student.certificates.my')
            ->with('success', 'Certificate request submitted successfully!');
    }

    /**
     * My Certificates — list all requests.
     */
    public function myCertificates(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = CertificateRequest::where('student_id', $request->user()->id)
            ->with('certificateType')
            ->orderByDesc('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $certificates = $query->paginate(15);

        return view('student.my-certificates', compact('certificates', 'status'));
    }

    /**
     * Download an approved certificate PDF.
     */
    public function download(CertificateRequest $certificateRequest)
    {
        if ($certificateRequest->student_id !== auth()->id()) {
            abort(403);
        }

        if (!$certificateRequest->isApproved() || !$certificateRequest->file_path) {
            return back()->with('error', 'Certificate is not available for download.');
        }

        return Storage::disk('public')->download(
            $certificateRequest->file_path,
            $certificateRequest->certificate_number . '.pdf'
        );
    }
}
