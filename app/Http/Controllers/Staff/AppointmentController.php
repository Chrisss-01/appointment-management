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
        $filter = $request->get('filter', 'today');

        $query = Appointment::where('staff_id', $request->user()->id)
            ->where('status', 'approved')
            ->with(['student', 'service']);

        if ($filter === 'today') {
            $query->whereDate('date', today());
        } else {
            // "Upcoming" = today + future
            $query->whereDate('date', '>=', today());
        }

        $appointments = $query->orderBy('date')
            ->orderBy('queue_number')
            ->orderBy('start_time')
            ->get(); // Using get() instead of paginate for the list view

        return view('staff.record-visits', compact('appointments', 'filter'));
    }

    /**
     * Get consultation data for slide-over.
     */
    public function showConsultation(Request $request, Appointment $appointment)
    {
        if ($appointment->staff_id !== $request->user()->id) {
            abort(403);
        }

        $appointment->load(['student', 'service']);

        $patientHistory = \App\Models\MedicalRecord::where('student_id', $appointment->student_id)
            ->with('appointment.service')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return response()->json([
            'appointment' => $appointment,
            'history' => $patientHistory,
        ]);
    }

    /**
     * Store dynamic consultation form.
     */
    public function storeConsultation(Request $request, Appointment $appointment)
    {
        if ($appointment->staff_id !== $request->user()->id) {
            abort(403);
        }

        $formType = $appointment->service->form_type ?? 'standard_consultation';

        // Base validation
        $rules = [
            'notes' => 'nullable|string|max:2000',
            'vital_signs' => 'nullable|array',
            'vital_signs.blood_pressure' => 'nullable|string|max:20',
            'vital_signs.temperature' => 'nullable|numeric|min:30|max:45',
            'vital_signs.heart_rate' => 'nullable|integer|min:30|max:250',
            'vital_signs.weight' => 'nullable|numeric|min:1|max:500',
            'vital_signs.height' => 'nullable|numeric|min:30|max:300',
        ];

        // Specific validation based on form type
        if ($formType === 'standard_consultation') {
            $rules = array_merge($rules, [
                'chief_complaint' => 'nullable|string|max:1000',
                'diagnosis' => 'nullable|string|max:1000',
                'treatment' => 'nullable|string|max:1000',
                'prescription' => 'nullable|string|max:1000',
            ]);
        } elseif ($formType === 'eye_checkup') {
             $rules = array_merge($rules, [
                'chief_complaint' => 'nullable|string|max:1000',
                'diagnosis' => 'nullable|string|max:1000',
                'treatment' => 'nullable|string|max:1000',
                'prescription' => 'nullable|string|max:1000',
                'visual_acuity' => 'nullable|array',
            ]);
        }

        $validated = $request->validate($rules);

        // Create the medical record
        $recordType = match($formType) {
            'standard_consultation', 'eye_checkup' => 'consultation',
            default => 'general'
        };

        \App\Models\MedicalRecord::create([
            'student_id' => $appointment->student_id,
            'staff_id' => $request->user()->id,
            'appointment_id' => $appointment->id,
            'record_type' => $recordType,
            'chief_complaint' => $validated['chief_complaint'] ?? null,
            'diagnosis' => $validated['diagnosis'] ?? null,
            'treatment' => $validated['treatment'] ?? null,
            'prescription' => $validated['prescription'] ?? null,
            'vital_signs' => $validated['vital_signs'] ?? null,
            'visual_acuity' => $validated['visual_acuity'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Mark appointment as completed
        $appointment->complete($validated['notes'] ?? null);

        Notification::send(
            $appointment->student_id,
            'appointment_completed',
            'Visit Completed',
            "Your visit for {$appointment->service->name} on {$appointment->date->format('M d, Y')} has been completed.",
            ['appointment_id' => $appointment->id]
        );

        return back()->with('success', 'Consultation saved and appointment marked as complete.');
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
