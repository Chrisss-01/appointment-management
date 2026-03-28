<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Notification;
use App\Services\BookingService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        private BookingService $bookingService
    ) {}

    /**
     * List appointment requests for this staff member.
     */
    public function requests(Request $request)
    {
        $status = $request->get('status', 'pending');

        $appointments = Appointment::where('staff_id', $request->user()->id)
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->with(['student', 'service'])
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->paginate(15);

        return view('staff.appointments', compact('appointments', 'status'));
    }

    /**
     * Approve an appointment.
     */
    public function approve(Request $request, Appointment $appointment)
    {
        if ($appointment->staff_id !== $request->user()->id) {
            abort(403);
        }

        if (!$appointment->isPending()) {
            return back()->with('error', 'This appointment cannot be approved.');
        }

        $appointment->approve();

        Notification::send(
            $appointment->student_id,
            'appointment_approved',
            'Appointment Approved',
            "Your appointment for {$appointment->service->name} on {$appointment->date->format('M d, Y')} at {$appointment->start_time} has been approved.",
            ['appointment_id' => $appointment->id]
        );

        return back()->with('success', 'Appointment approved.');
    }

    /**
     * Reject an appointment.
     */
    public function reject(Request $request, Appointment $appointment)
    {
        if ($appointment->staff_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $appointment->reject($validated['rejection_reason']);

        // Free up the slot
        $appointment->generatedSlot?->markAsAvailable();

        Notification::send(
            $appointment->student_id,
            'appointment_rejected',
            'Appointment Rejected',
            "Your appointment for {$appointment->service->name} on {$appointment->date->format('M d, Y')} has been rejected. Reason: {$validated['rejection_reason']}",
            ['appointment_id' => $appointment->id]
        );

        return back()->with('success', 'Appointment rejected.');
    }

    /**
     * Record a visit / mark as completed.
     */
    public function recordVisit(Request $request)
    {
        $appointments = Appointment::where('staff_id', $request->user()->id)
            ->where('status', 'approved')
            ->with(['student', 'service'])
            ->orderBy('date')
            ->orderBy('queue_number')
            ->paginate(15);

        return view('staff.record-visits', compact('appointments'));
    }

    /**
     * Complete an appointment.
     */
    public function complete(Request $request, Appointment $appointment)
    {
        if ($appointment->staff_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'staff_notes' => 'nullable|string|max:1000',
        ]);

        $appointment->complete($validated['staff_notes'] ?? null);

        Notification::send(
            $appointment->student_id,
            'appointment_completed',
            'Visit Completed',
            "Your visit for {$appointment->service->name} on {$appointment->date->format('M d, Y')} has been completed.",
            ['appointment_id' => $appointment->id]
        );

        return back()->with('success', 'Visit recorded successfully.');
    }

    /**
     * Mark appointment as no-show.
     */
    public function noShow(Request $request, Appointment $appointment)
    {
        if ($appointment->staff_id !== $request->user()->id) {
            abort(403);
        }

        $appointment->update(['status' => 'no_show']);

        Notification::send(
            $appointment->student_id,
            'appointment_no_show',
            'Marked as No-Show',
            "You were marked as no-show for your appointment on {$appointment->date->format('M d, Y')}.",
            ['appointment_id' => $appointment->id]
        );

        return back()->with('success', 'Appointment marked as no-show.');
    }
}
