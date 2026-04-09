<?php

// Lightweight healthcheck endpoint for Railway — always returns 200
Route::get('/health', fn () => response('OK', 200));

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\AppointmentController as StudentAppointment;
use App\Http\Controllers\Staff\DashboardController as StaffDashboard;
use App\Http\Controllers\Staff\AvailabilityController;
use App\Http\Controllers\Staff\AppointmentController as StaffAppointment;
use App\Http\Controllers\Staff\PatientController;
use App\Http\Controllers\Staff\ClinicTaskController;
use App\Http\Controllers\Staff\CertificateController as StaffCertificate;
use App\Http\Controllers\Staff\AnnouncementController as StaffAnnouncement;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointment;
use App\Http\Controllers\Admin\CertificateTypeController;
use App\Http\Controllers\Admin\ReasonPresetController;
use App\Http\Controllers\Admin\DoctorSignatureController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Student\OnboardingController;
use App\Http\Controllers\Student\CertificateController as StudentCertificate;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

// ──────────────────────────────────────────────────────────────────
// PUBLIC / GUEST ROUTES
// ──────────────────────────────────────────────────────────────────

Route::view('/', 'landing')->name('landing');



// Public certificate verification
Route::get('/certificates/verify/{certificateNumber}', [CertificateVerificationController::class, 'verify'])->name('certificates.verify');

// Public Legal Pages
Route::get('/privacy-policy', [PublicPageController::class, 'privacy'])->name('legal.privacy');
Route::get('/terms-of-service', [PublicPageController::class, 'terms'])->name('legal.terms');

// Public Clinic Schedule
Route::get('/clinic-schedule', [PublicPageController::class, 'schedule'])->name('public.schedule');

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/verify-otp', [OtpVerificationController::class, 'show'])->name('otp.show');
    Route::post('/verify-otp', [OtpVerificationController::class, 'verify'])->name('otp.verify');
    Route::post('/resend-otp', [OtpVerificationController::class, 'resend'])->name('otp.resend');
    Route::get('/staff/login', [AuthController::class, 'showStaffLoginForm'])->name('staff.login');
    Route::post('/staff/login', [AuthController::class, 'staffLogin'])->name('staff.login.submit');

    // Password Reset
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:3,10') // 3 requests per 10 mins
        ->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ──────────────────────────────────────────────────────────────────
// ONBOARDING ROUTES
// ──────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/onboarding/department', [OnboardingController::class, 'showDepartment'])->name('onboarding.department');
    Route::get('/onboarding/program', [OnboardingController::class, 'showProgram'])->name('onboarding.program');
    Route::get('/onboarding/year-level', [OnboardingController::class, 'showYearLevel'])->name('onboarding.year-level');
    Route::get('/onboarding/student-id', [OnboardingController::class, 'showStudentId'])->name('onboarding.student-id');
    Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
    Route::get('/onboarding/success', [OnboardingController::class, 'showSuccess'])->name('onboarding.success');
});

// ──────────────────────────────────────────────────────────────────
// STUDENT ROUTES
// ──────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:student', 'check.onboarding'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
    Route::get('/health', [StudentDashboard::class, 'health'])->name('health');
    Route::get('/announcements', [StudentDashboard::class, 'announcements'])->name('announcements');
    Route::get('/profile', [StudentDashboard::class, 'profile'])->name('profile');
    Route::put('/profile', [StudentDashboard::class, 'updateProfile'])->name('profile.update');

    // Medical Services & Appointments
    Route::get('/services', [StudentAppointment::class, 'services'])->name('services');
    Route::get('/services/{service}', [StudentAppointment::class, 'showService'])->name('services.show');
    Route::get('/services/{service}/available-dates', [StudentAppointment::class, 'getAvailableDates'])->name('services.available-dates');
    Route::get('/services/{service}/available-slots', [StudentAppointment::class, 'getAvailableSlots'])->name('services.available-slots');
    Route::post('/appointments/book', [StudentAppointment::class, 'book'])->name('appointments.book');
    Route::get('/appointments', [StudentAppointment::class, 'myAppointments'])->name('appointments');
    Route::patch('/appointments/{appointment}/cancel', [StudentAppointment::class, 'cancel'])->name('appointments.cancel');

    // Certificates
    Route::get('/certificates/request', [StudentCertificate::class, 'requestIndex'])->name('certificates.request');
    Route::get('/certificates/request/{certificateType}', [StudentCertificate::class, 'requestForm'])->name('certificates.request.form');
    Route::post('/certificates/request/{certificateType}', [StudentCertificate::class, 'submitRequest'])->name('certificates.request.submit');
    Route::get('/certificates', [StudentCertificate::class, 'myCertificates'])->name('certificates.my');
    Route::get('/certificates/{certificateRequest}/download', [StudentCertificate::class, 'download'])->name('certificates.download');
});

// ──────────────────────────────────────────────────────────────────
// STAFF ROUTES
// ──────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboard::class, 'index'])->name('dashboard');
    Route::get('/announcements', [StaffAnnouncement::class, 'index'])->name('announcements');
    Route::post('/announcements', [StaffAnnouncement::class, 'store'])->name('announcements.store');
    Route::put('/announcements/{announcement}', [StaffAnnouncement::class, 'update'])->name('announcements.update');
    Route::patch('/announcements/{announcement}/toggle', [StaffAnnouncement::class, 'togglePublish'])->name('announcements.toggle');
    Route::delete('/announcements/{announcement}', [StaffAnnouncement::class, 'destroy'])->name('announcements.destroy');
    Route::get('/profile', [StaffDashboard::class, 'profile'])->name('profile');
    Route::put('/profile', [StaffDashboard::class, 'updateProfile'])->name('profile.update');

    // Clinical — Availability
    Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability');
    Route::get('/availability/calendar-data', [AvailabilityController::class, 'calendarData'])->name('availability.calendar');
    Route::get('/availability/by-date', [AvailabilityController::class, 'getByDate'])->name('availability.by-date');
    Route::get('/availability/services', [AvailabilityController::class, 'servicesWithAvailability'])->name('availability.services');
    Route::get('/availability/list', [AvailabilityController::class, 'upcomingList'])->name('availability.list');
    Route::post('/availability', [AvailabilityController::class, 'store'])->name('availability.store');
    Route::post('/availability/bulk', [AvailabilityController::class, 'storeBulk'])->name('availability.store-bulk');
    Route::patch('/availability/{availabilitySlot}', [AvailabilityController::class, 'update'])->name('availability.update');
    Route::delete('/availability/{availabilitySlot}', [AvailabilityController::class, 'destroy'])->name('availability.destroy');
    Route::get('/availability/{availabilitySlot}/check-bookings', [AvailabilityController::class, 'checkBookings'])->name('availability.check-bookings');
    Route::get('/availability/{availabilitySlot}/slots', [AvailabilityController::class, 'slots'])->name('availability.slots');

    // Clinical — Appointments
    Route::get('/appointments', [StaffAppointment::class, 'requests'])->name('appointments');
    Route::patch('/appointments/{appointment}/approve', [StaffAppointment::class, 'approve'])->name('appointments.approve');
    Route::patch('/appointments/{appointment}/reject', [StaffAppointment::class, 'reject'])->name('appointments.reject');
    Route::patch('/appointments/{appointment}/complete', [StaffAppointment::class, 'complete'])->name('appointments.complete');
    Route::patch('/appointments/{appointment}/no-show', [StaffAppointment::class, 'noShow'])->name('appointments.no-show');

    // Clinical — Record Visits
    Route::get('/record-visits', [StaffAppointment::class, 'recordVisit'])->name('record-visits');
    Route::get('/record-visits/{appointment}/consultation', [StaffAppointment::class, 'showConsultation'])->name('record-visits.consultation');
    Route::post('/record-visits/{appointment}/consultation', [StaffAppointment::class, 'storeConsultation'])->name('record-visits.consultation.store');

    // Clinical — Patients
    Route::get('/patients', [PatientController::class, 'index'])->name('patients');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::post('/patients/medical-record', [PatientController::class, 'storeMedicalRecord'])->name('patients.medical-record');

    // Workflow — Clinic Tasks
    Route::get('/tasks', [ClinicTaskController::class, 'index'])->name('tasks');
    Route::post('/tasks', [ClinicTaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}/status', [ClinicTaskController::class, 'updateStatus'])->name('tasks.status');
    Route::delete('/tasks/{task}', [ClinicTaskController::class, 'destroy'])->name('tasks.destroy');

    // Certificate Requests
    Route::get('/certificate-requests', [StaffCertificate::class, 'index'])->name('certificate-requests');
    Route::get('/certificate-requests/{certificateRequest}', [StaffCertificate::class, 'show'])->name('certificate-requests.show');
    Route::patch('/certificate-requests/{certificateRequest}/verify', [StaffCertificate::class, 'verifyDocuments'])->name('certificate-requests.verify');
    Route::patch('/certificate-requests/{certificateRequest}/reject', [StaffCertificate::class, 'reject'])->name('certificate-requests.reject');
    Route::patch('/certificate-requests/{certificateRequest}/approve', [StaffCertificate::class, 'approve'])->name('certificate-requests.approve');
});

// ──────────────────────────────────────────────────────────────────
// ADMIN ROUTES
// ──────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/students', [UserManagementController::class, 'students'])->name('students');
    Route::get('/staff', [UserManagementController::class, 'staff'])->name('staff');
    Route::post('/staff', [UserManagementController::class, 'createStaff'])->name('staff.create');
    Route::patch('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Service Management
    Route::get('/services', [ServiceController::class, 'index'])->name('services');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::post('/services/{service}/reason-presets', [ServiceController::class, 'storeReasonPreset'])->name('services.reason-presets.store');
    Route::put('/service-reason-presets/{reasonPreset}', [ServiceController::class, 'updateReasonPreset'])->name('services.reason-presets.update');
    Route::delete('/service-reason-presets/{reasonPreset}', [ServiceController::class, 'destroyReasonPreset'])->name('services.reason-presets.destroy');

    // Appointments
    Route::get('/appointments', [AdminAppointment::class, 'index'])->name('appointments');

    // Certificate Type Management
    Route::get('/certificate-types', [CertificateTypeController::class, 'index'])->name('certificate-types');
    Route::post('/certificate-types', [CertificateTypeController::class, 'store'])->name('certificate-types.store');
    Route::put('/certificate-types/{certificateType}', [CertificateTypeController::class, 'update'])->name('certificate-types.update');
    Route::delete('/certificate-types/{certificateType}', [CertificateTypeController::class, 'destroy'])->name('certificate-types.destroy');
    Route::post('/certificate-types/{certificateType}/documents', [CertificateTypeController::class, 'storeDocument'])->name('certificate-types.documents.store');
    Route::delete('/certificate-type-documents/{document}', [CertificateTypeController::class, 'destroyDocument'])->name('certificate-types.documents.destroy');
    Route::post('/certificate-types/{certificateType}/purposes', [CertificateTypeController::class, 'storePurpose'])->name('certificate-types.purposes.store');
    Route::delete('/certificate-purpose-presets/{preset}', [CertificateTypeController::class, 'destroyPurpose'])->name('certificate-types.purposes.destroy');

    // Reason Presets (redirect to Services page — management is now inline)
    Route::get('/reason-presets', function () {
        return redirect()->route('admin.services');
    })->name('reason-presets');

    // Doctor Signatures
    Route::get('/doctor-signatures', [DoctorSignatureController::class, 'index'])->name('doctor-signatures');
    Route::put('/doctor-signatures/{user}', [DoctorSignatureController::class, 'update'])->name('doctor-signatures.update');

    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::patch('/announcements/{announcement}/toggle', [AnnouncementController::class, 'togglePublish'])->name('announcements.toggle');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // Reports
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
});

// ──────────────────────────────────────────────────────────────────
// API ROUTES (authenticated, JSON)
// ──────────────────────────────────────────────────────────────────

Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});