<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\CertificateRequest;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(
        private CertificateService $certificateService
    ) {}

    /**
     * List certificate requests for staff review.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $search = $request->get('search');

        $query = CertificateRequest::with(['student', 'certificateType', 'documents']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('student', fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
            );
        }

        $certificateRequests = $query->orderByDesc('created_at')->paginate(15);

        return view('staff.certificate-requests', compact('certificateRequests', 'status', 'search'));
    }

    /**
     * Show a single certificate request detail.
     */
    public function show(CertificateRequest $certificateRequest)
    {
        $certificateRequest->load([
            'student',
            'student.medicalRecords' => fn($q) => $q->orderByDesc('created_at'),
            'certificateType.requiredDocuments',
            'documents.typeDocument',
            'verifiedByUser',
            'approvedByUser',
        ]);

        return view('staff.certificate-request-detail', compact('certificateRequest'));
    }

    /**
     * Verify documents (nurse action).
     */
    public function verifyDocuments(Request $request, CertificateRequest $certificateRequest)
    {
        $user = $request->user();

        if (!$certificateRequest->isPending()) {
            return back()->with('error', 'This request cannot be verified at this stage.');
        }

        $this->certificateService->verifyDocuments($certificateRequest, $user);

        return back()->with('success', 'Documents verified successfully.');
    }

    /**
     * Reject a request (nurse or doctor).
     */
    public function reject(Request $request, CertificateRequest $certificateRequest)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($certificateRequest->isApproved()) {
            return back()->with('error', 'Cannot reject an approved certificate.');
        }

        $this->certificateService->reject(
            $certificateRequest,
            $request->user(),
            $validated['rejection_reason']
        );

        return back()->with('success', 'Certificate request rejected.');
    }

    /**
     * Approve a request (doctor only).
     */
    public function approve(Request $request, CertificateRequest $certificateRequest)
    {
        $user = $request->user();

        if (!$user->isDoctor()) {
            return back()->with('error', 'Only doctors can approve certificates.');
        }

        if (!in_array($certificateRequest->status, ['pending', 'documents_verified'])) {
            return back()->with('error', 'This request cannot be approved at this stage.');
        }

        $this->certificateService->approve($certificateRequest, $user);

        return back()->with('success', 'Certificate approved and PDF generated.');
    }
}
