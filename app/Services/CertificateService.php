<?php

namespace App\Services;

use App\Models\CertificateRequest;
use App\Models\Notification;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateService
{
    /**
     * Verify documents for a certificate request (nurse action).
     */
    public function verifyDocuments(CertificateRequest $request, User $nurse): void
    {
        $request->update([
            'status' => 'documents_verified',
            'verified_by' => $nurse->id,
            'verified_at' => now(),
        ]);

        Notification::send(
            $request->student_id,
            'certificate_docs_verified',
            'Documents Verified',
            "Your documents for {$request->certificateType->name} have been verified and are awaiting doctor approval.",
            ['certificate_request_id' => $request->id]
        );
    }

    /**
     * Reject a certificate request.
     */
    public function reject(CertificateRequest $request, User $staff, string $reason): void
    {
        $request->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        Notification::send(
            $request->student_id,
            'certificate_rejected',
            'Certificate Request Rejected',
            "Your {$request->certificateType->name} request has been rejected. Reason: {$reason}",
            ['certificate_request_id' => $request->id]
        );
    }

    /**
     * Approve a certificate request and generate PDF (doctor action).
     */
    public function approve(CertificateRequest $request, User $doctor): CertificateRequest
    {
        $certificateNumber = CertificateRequest::generateCertificateNumber();
        $verificationUrl = url("/certificates/verify/{$certificateNumber}");

        // Generate QR code as base64
        $qrCodeBase64 = base64_encode(
            QrCode::format('svg')->size(150)->generate($verificationUrl)
        );

        // Update request
        $request->update([
            'status' => 'approved',
            'approved_by' => $doctor->id,
            'approved_at' => now(),
            'certificate_number' => $certificateNumber,
            'qr_code' => $verificationUrl,
        ]);

        // Generate PDF
        $pdfPath = $this->generatePdf($request, $doctor, $qrCodeBase64);
        $request->update(['file_path' => $pdfPath]);

        Notification::send(
            $request->student_id,
            'certificate_approved',
            'Certificate Approved',
            "Your {$request->certificateType->name} has been approved. Certificate #{$certificateNumber} is ready for download.",
            ['certificate_request_id' => $request->id]
        );

        return $request->fresh();
    }

    /**
     * Generate the certificate PDF.
     */
    private function generatePdf(CertificateRequest $request, User $doctor, string $qrCodeBase64): string
    {
        $request->load(['student', 'certificateType']);

        $signatureBase64 = null;
        if ($doctor->signature_image && Storage::disk('public')->exists($doctor->signature_image)) {
            $signatureBase64 = base64_encode(Storage::disk('public')->get($doctor->signature_image));
            $extension = pathinfo($doctor->signature_image, PATHINFO_EXTENSION);
            $signatureBase64 = "data:image/{$extension};base64,{$signatureBase64}";
        }

        $data = [
            'certificateNumber' => $request->certificate_number,
            'studentName' => $request->student->name,
            'studentId' => $request->student->student_id,
            'purpose' => $request->purpose,
            'issueDate' => $request->approved_at->format('F d, Y'),
            'clinicName' => 'UV Toledo Clinic',
            'doctorName' => $doctor->name,
            'licenseNumber' => $doctor->license_number,
            'signatureBase64' => $signatureBase64,
            'qrCodeBase64' => $qrCodeBase64,
            'certificateType' => $request->certificateType->name,
        ];

        $pdf = Pdf::loadView('certificates.pdf-template', $data);
        $pdf->setPaper('A4', 'portrait');

        $fileName = "certificates/{$request->certificate_number}.pdf";
        Storage::disk('public')->put($fileName, $pdf->output());

        return $fileName;
    }
}
