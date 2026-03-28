<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification | UV Toledo Clinic</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
</head>
<body class="min-h-screen bg-[#0F0F0F] text-white antialiased flex items-center justify-center p-6">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-[#1392EC]/10 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[#1392EC]" style="font-size:28px;">verified</span>
            </div>
            <h1 class="text-xl font-bold text-white">Certificate Verification</h1>
            <p class="text-sm text-gray-500 mt-1">UV Toledo Clinic Management System</p>
        </div>

        @if($certificate)
        <div class="bg-[#1A1A1A] border border-emerald-500/20 rounded-2xl overflow-hidden">
            <div class="bg-emerald-500/10 px-6 py-4 flex items-center gap-3">
                <span class="material-symbols-outlined text-emerald-400" style="font-size:24px;">check_circle</span>
                <div>
                    <p class="text-sm font-semibold text-emerald-400">Valid Certificate</p>
                    <p class="text-xs text-gray-400">This certificate is authentic and verified</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Certificate Number</p>
                    <p class="text-sm text-white font-mono font-medium">{{ $certificate->certificate_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Certificate Type</p>
                    <p class="text-sm text-white">{{ $certificate->certificateType->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Issued To</p>
                    <p class="text-sm text-white">{{ $certificate->student->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Date Issued</p>
                    <p class="text-sm text-white">{{ $certificate->approved_at?->format('F d, Y') }}</p>
                </div>
                @if($certificate->approvedByUser)
                <div>
                    <p class="text-xs text-gray-500 mb-1">Approved By</p>
                    <p class="text-sm text-white">{{ $certificate->approvedByUser->name }}</p>
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="bg-[#1A1A1A] border border-red-500/20 rounded-2xl overflow-hidden">
            <div class="bg-red-500/10 px-6 py-4 flex items-center gap-3">
                <span class="material-symbols-outlined text-red-400" style="font-size:24px;">error</span>
                <div>
                    <p class="text-sm font-semibold text-red-400">Certificate Not Found</p>
                    <p class="text-xs text-gray-400">No valid certificate matches this number</p>
                </div>
            </div>
            <div class="p-6 text-center">
                <p class="text-sm text-gray-400 mb-1">Certificate Number</p>
                <p class="text-sm text-white font-mono">{{ $certificateNumber }}</p>
                <p class="text-xs text-gray-500 mt-4">This certificate may be invalid, expired, or has not yet been approved. Please contact the clinic for more information.</p>
            </div>
        </div>
        @endif

        <p class="text-center text-xs text-gray-600 mt-6">&copy; {{ date('Y') }} UV Toledo Clinic. All rights reserved.</p>
    </div>
</body>
</html>
