<?php

namespace App\Http\Controllers;

use App\Models\CertificateRequest;

class CertificateVerificationController extends Controller
{
    /**
     * Public certificate verification page.
     */
    public function verify(string $certificateNumber)
    {
        $certificate = CertificateRequest::where('certificate_number', $certificateNumber)
            ->where('status', 'approved')
            ->with(['student', 'certificateType'])
            ->first();

        return view('certificates.verify', compact('certificate', 'certificateNumber'));
    }
}
