<?php

namespace Tests\Feature;

use App\Models\CertificateRequest;
use App\Models\CertificateType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateApprovalFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:' . base64_encode(random_bytes(32)),
        ]);
    }

    public function test_doctor_can_approve_certificate_with_preset_findings_and_generate_pdf(): void
    {
        Storage::fake('public');

        $doctor = User::factory()->create([
            'role' => 'staff',
            'staff_type' => 'doctor',
            'is_active' => true,
            'license_number' => 'LIC-12345',
        ]);

        $certificateRequest = $this->createCertificateRequest();

        $response = $this->actingAs($doctor)->patch(
            route('staff.certificate-requests.approve', $certificateRequest),
            ['doctor_findings_option' => 'Clinically stable']
        );

        $response->assertSessionHas('success');

        $certificateRequest->refresh();

        $this->assertSame('approved', $certificateRequest->status);
        $this->assertSame('Clinically stable', $certificateRequest->doctor_findings);
        $this->assertNotNull($certificateRequest->certificate_number);
        $this->assertNotNull($certificateRequest->approved_at);
        $this->assertNotNull($certificateRequest->file_path);
        $this->assertSame('Fit for OJT/Internship', $certificateRequest->remarks_recommendation);
        Storage::disk('public')->assertExists($certificateRequest->file_path);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $certificateRequest->student_id,
            'type' => 'certificate_approved',
        ]);
    }

    public function test_other_findings_require_custom_text_and_store_it(): void
    {
        Storage::fake('public');

        $doctor = User::factory()->create([
            'role' => 'staff',
            'staff_type' => 'doctor',
            'is_active' => true,
        ]);

        $certificateRequest = $this->createCertificateRequest([
            'purpose_type' => 'other',
            'purpose_text' => 'Field Work',
        ]);

        $invalidResponse = $this->from(route('staff.certificate-requests.show', $certificateRequest))
            ->actingAs($doctor)
            ->patch(route('staff.certificate-requests.approve', $certificateRequest), [
                'doctor_findings_option' => 'other',
            ]);

        $invalidResponse->assertSessionHasErrors('doctor_findings_other');

        $validResponse = $this->from(route('staff.certificate-requests.show', $certificateRequest))
            ->actingAs($doctor)
            ->patch(route('staff.certificate-requests.approve', $certificateRequest), [
                'doctor_findings_option' => 'other',
                'doctor_findings_other' => 'Recovered well after reevaluation',
            ]);

        $validResponse->assertSessionHas('success');

        $certificateRequest->refresh();

        $this->assertSame('Recovered well after reevaluation', $certificateRequest->doctor_findings);
        $this->assertSame('Fit for Field Work', $certificateRequest->remarks_recommendation);
    }

    public function test_staff_can_download_only_approved_generated_certificates(): void
    {
        Storage::fake('public');

        $staff = User::factory()->create([
            'role' => 'staff',
            'staff_type' => 'nurse',
            'is_active' => true,
        ]);

        $approvedRequest = $this->createCertificateRequest([
            'status' => 'approved',
            'certificate_number' => 'UVTC-CLINIC-2026-0001',
            'approved_at' => now(),
            'file_path' => 'certificates/UVTC-CLINIC-2026-0001.pdf',
            'doctor_findings' => 'No significant findings',
        ]);
        Storage::disk('public')->put($approvedRequest->file_path, 'pdf-content');

        $downloadResponse = $this->actingAs($staff)->get(
            route('staff.certificate-requests.download', $approvedRequest)
        );

        $downloadResponse->assertOk();
        $downloadResponse->assertDownload('UVTC-CLINIC-2026-0001.pdf');

        $pendingRequest = $this->createCertificateRequest([
            'status' => 'pending',
            'file_path' => null,
            'certificate_number' => null,
        ]);

        $blockedResponse = $this->from(route('staff.certificate-requests.show', $pendingRequest))
            ->actingAs($staff)
            ->get(route('staff.certificate-requests.download', $pendingRequest));

        $blockedResponse->assertRedirect(route('staff.certificate-requests.show', $pendingRequest));
        $blockedResponse->assertSessionHas('error', 'Certificate is not available for download.');
    }

    public function test_pdf_template_renders_purpose_findings_and_derived_remarks(): void
    {
        $html = view('certificates.pdf-template', [
            'certificateNumber' => 'UVTC-CLINIC-2026-0001',
            'studentName' => 'Test Student',
            'studentId' => '2026-0001',
            'purpose' => 'OJT/Internship',
            'doctorFindings' => 'Cleared after assessment',
            'remarksRecommendation' => 'Fit for OJT/Internship',
            'issueDate' => 'April 24, 2026',
            'clinicName' => 'UV Toledo Clinic',
            'doctorName' => 'Dr. Sample',
            'licenseNumber' => 'LIC-999',
            'signatureBase64' => null,
            'qrCodeBase64' => base64_encode('<svg></svg>'),
            'certificateType' => 'Medical Certificate',
        ])->render();

        $this->assertStringContainsString('OJT/Internship', $html);
        $this->assertStringContainsString('Cleared after assessment', $html);
        $this->assertStringContainsString('Fit for OJT/Internship', $html);
    }

    private function createCertificateRequest(array $overrides = []): CertificateRequest
    {
        $student = User::factory()->create([
            'role' => 'student',
            'student_id' => fake()->unique()->numerify('2026####'),
        ]);

        $certificateType = CertificateType::create([
            'name' => 'Medical Certificate',
            'slug' => fake()->unique()->slug(),
            'description' => 'Certificate for medical clearance.',
            'color' => '#1392EC',
            'icon' => 'description',
            'is_active' => true,
        ]);

        return CertificateRequest::create(array_merge([
            'student_id' => $student->id,
            'certificate_type_id' => $certificateType->id,
            'purpose_type' => 'OJT/Internship',
            'purpose_text' => null,
            'medical_history' => null,
            'additional_notes' => null,
            'doctor_findings' => null,
            'status' => 'pending',
        ], $overrides));
    }
}
