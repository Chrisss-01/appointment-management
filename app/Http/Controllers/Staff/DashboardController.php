<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CertificateRequest;
use App\Models\ClinicTask;
use App\Models\GeneratedSlot;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Staff dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();

        $todayAppointments = Appointment::where('staff_id', $user->id)
            ->where('date', $today)
            ->with(['student', 'service'])
            ->orderBy('start_time')
            ->get();

        $pendingRequests = Appointment::where('staff_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $todayCompleted = Appointment::where('staff_id', $user->id)
            ->where('status', 'completed')
            ->whereDate('completed_at', $today)
            ->count();

        $availableSlotsToday = GeneratedSlot::where('staff_id', $user->id)
            ->where('date', $today)
            ->where('status', 'available')
            ->count();

        $pendingTasks = ClinicTask::forUser($user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        $pendingCertificateRequests = CertificateRequest::whereIn('status', ['pending', 'documents_verified'])->count();

        $unreadNotifications = Notification::where('user_id', $user->id)
            ->unread()
            ->count();

        return view('staff.dashboard', compact(
            'todayAppointments',
            'pendingRequests',
            'todayCompleted',
            'availableSlotsToday',
            'pendingTasks',
            'pendingCertificateRequests',
            'unreadNotifications'
        ));
    }

    /**
     * Announcements visible to staff.
     */
    public function announcements()
    {
        $announcements = \App\Models\Announcement::published()
            ->forAudience('staff')
            ->with('author')
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('staff.announcements', compact('announcements'));
    }

    /**
     * Staff profile.
     */
    public function profile(Request $request)
    {
        return view('staff.profile', ['user' => $request->user()]);
    }

    /**
     * Update staff profile.
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
