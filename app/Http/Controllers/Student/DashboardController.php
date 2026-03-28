<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Appointment;
use App\Models\Certificate;
use App\Models\CertificateRequest;
use App\Models\MedicalRecord;
use App\Models\Notification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Student dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $upcomingAppointments = Appointment::where('student_id', $user->id)
            ->upcoming()
            ->with(['service', 'staff'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        $recentVisits = Appointment::where('student_id', $user->id)
            ->where('status', 'completed')
            ->with(['service', 'staff'])
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        $pendingCertificates = Certificate::where('student_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->count()
            + CertificateRequest::where('student_id', $user->id)
            ->whereIn('status', ['pending', 'documents_verified'])
            ->count();

        $announcements = Announcement::published()
            ->forAudience('student')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $unreadNotifications = Notification::where('user_id', $user->id)
            ->unread()
            ->count();

        return view('student.dashboard', compact(
            'upcomingAppointments',
            'recentVisits',
            'pendingCertificates',
            'announcements',
            'unreadNotifications'
        ));
    }

    /**
     * Student health records.
     */
    public function health(Request $request)
    {
        $records = MedicalRecord::where('student_id', $request->user()->id)
            ->with(['staff', 'appointment.service'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('student.health', compact('records'));
    }

    /**
     * Student visit history.
     */
    public function visitHistory(Request $request)
    {
        $visits = Appointment::where('student_id', $request->user()->id)
            ->with(['service', 'staff'])
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->paginate(15);

        return view('student.visit-history', compact('visits'));
    }

    /**
     * Student certificates.
     */
    public function certificates(Request $request)
    {
        $certificates = Certificate::where('student_id', $request->user()->id)
            ->with(['staff', 'appointment.service'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('student.certificates', compact('certificates'));
    }

    /**
     * Announcements visible to students.
     */
    public function announcements()
    {
        $announcements = Announcement::published()
            ->forAudience('student')
            ->with('author')
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('student.announcements', compact('announcements'));
    }

    /**
     * Student profile.
     */
    public function profile(Request $request)
    {
        return view('student.profile', ['user' => $request->user()]);
    }

    /**
     * Update student profile.
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $request->user()->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }
}
