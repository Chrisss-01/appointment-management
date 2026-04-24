<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 40px 50px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1a1a1a; font-size: 12px; line-height: 1.6; }
        .header { text-align: center; border-bottom: 3px solid #1392EC; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { font-size: 22px; color: #1392EC; margin: 0 0 5px; letter-spacing: 1px; }
        .header h2 { font-size: 14px; color: #555; margin: 0 0 3px; font-weight: normal; }
        .header p { font-size: 10px; color: #888; margin: 2px 0 0; }
        .cert-number { text-align: right; font-size: 11px; color: #666; margin-bottom: 20px; }
        .cert-title { text-align: center; font-size: 20px; font-weight: bold; color: #1a1a1a; margin: 30px 0; text-transform: uppercase; letter-spacing: 2px; }
        .content { margin: 20px 30px; font-size: 13px; line-height: 1.8; }
        .content .label { color: #666; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
        .content .value { font-weight: 600; font-size: 14px; margin-bottom: 15px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table td { padding: 8px 12px; border-bottom: 1px solid #eee; }
        .info-table td:first-child { color: #666; width: 40%; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-table td:last-child { font-weight: 600; }
        .signature-section { margin-top: 50px; text-align: center; }
        .signature-line { width: 250px; border-top: 2px solid #1a1a1a; margin: 0 auto 5px; padding-top: 8px; }
        .signature-name { font-weight: bold; font-size: 14px; }
        .signature-title { font-size: 11px; color: #666; }
        .signature-image { max-height: 60px; margin-bottom: 5px; }
        .footer { margin-top: 40px; padding-top: 15px; border-top: 2px solid #1392EC; display: table; width: 100%; }
        .footer-left { display: table-cell; vertical-align: middle; width: 70%; }
        .footer-right { display: table-cell; vertical-align: middle; text-align: right; width: 30%; }
        .footer p { font-size: 9px; color: #888; margin: 2px 0; }
        .qr-code { text-align: center; }
        .qr-code img { width: 100px; height: 100px; }
        .qr-code p { font-size: 8px; color: #999; margin-top: 3px; }
        .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 80px; color: rgba(19, 146, 236, 0.04); font-weight: bold; letter-spacing: 10px; z-index: -1; }
    </style>
</head>
<body>
    <div class="watermark">UV TOLEDO CLINIC</div>

    <div class="header">
        <h1>UV Toledo Clinic</h1>
        <h2>University Health Services</h2>
        <p>University of the Visayas — Toledo Campus</p>
    </div>

    <div class="cert-number">
        Certificate No: <strong>{{ $certificateNumber }}</strong>
    </div>

    <div class="cert-title">{{ $certificateType }}</div>

    <div class="content">
        <table class="info-table">
            <tr>
                <td>Student Name</td>
                <td>{{ $studentName }}</td>
            </tr>
            <tr>
                <td>Student ID</td>
                <td>{{ $studentId ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Purpose</td>
                <td>{{ $purpose ?? 'General Purpose' }}</td>
            </tr>
            <tr>
                <td>Doctor's Findings</td>
                <td>{{ $doctorFindings ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Remarks/Recommendation</td>
                <td>{{ $remarksRecommendation ?? 'Fit for General Purpose' }}</td>
            </tr>
            <tr>
                <td>Date Issued</td>
                <td>{{ $issueDate }}</td>
            </tr>
            <tr>
                <td>Issuing Clinic</td>
                <td>{{ $clinicName }}</td>
            </tr>
        </table>
    </div>

    <div class="signature-section">
        @if($signatureBase64)
            <img src="{{ $signatureBase64 }}" class="signature-image" alt="Signature">
        @endif
        <div class="signature-line">
            <div class="signature-name">{{ $doctorName }}</div>
            <div class="signature-title">Attending Physician</div>
            @if($licenseNumber)
                <div class="signature-title">License No: {{ $licenseNumber }}</div>
            @endif
        </div>
    </div>

    <div class="footer">
        <div class="footer-left">
            <p><strong>{{ $clinicName }}</strong></p>
            <p>This certificate is digitally generated and verified.</p>
            <p>Verify at: {{ url('/certificates/verify/' . $certificateNumber) }}</p>
        </div>
        <div class="footer-right">
            <div class="qr-code">
                <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" alt="QR Code">
                <p>Scan to verify</p>
            </div>
        </div>
    </div>
</body>
</html>
