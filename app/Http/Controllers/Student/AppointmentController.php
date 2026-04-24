<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookAppointmentRequest;
use App\Models\Appointment;
use App\Models\GeneratedSlot;
use App\Models\Service;
use App\Services\BookingService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        private BookingService $bookingService
    ) {}

    /**
     * Show medical services page.
     */
    public function services()
    {
        $services = Service::active()
            ->where('slug', '!=', 'medical-certificate-request')
            ->get();

        return view('student.services', compact('services'));
    }

    /**
     * Show available dates/slots for a service.
     */
    public function showService(Service $service)
    {
        $activeAppointment = Appointment::where('student_id', request()->user()->id)
            ->where('service_id', $service->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        $reasonPresets = $service->reasonPresets()->get();

        return view('student.book-appointment', compact('service', 'reasonPresets', 'activeAppointment'));
    }

    /**
     * Get available slots for a service on a specific date (AJAX).
     */
    public function getAvailableSlots(Request $request, Service $service)
    {
        $request->validate(['date' => 'required|date|after_or_equal:today']);

        $slots = GeneratedSlot::query()
            ->forService($service->id)
            ->forDate($request->date)
            ->bookableForStudents()
            ->with('staff')
            ->orderBy('start_time')
            ->get()
            ->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'staff_name' => $slot->staff->name,
                ];
            });

        return response()->json($slots)->header('Cache-Control', 'no-store, no-cache');
    }

    /**
     * Get dates that have available slots for a service (AJAX).
     */
    public function getAvailableDates(Service $service)
    {
        $dates = GeneratedSlot::query()
            ->forService($service->id)
            ->bookableForStudents()
            ->selectRaw('date, COUNT(*) as slot_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date->format('Y-m-d'),
                    'slot_count' => $item->slot_count,
                ];
            });

        return response()->json($dates)->header('Cache-Control', 'no-store, no-cache');
    }

    /**
     * Book an appointment.
     */
    public function book(BookAppointmentRequest $request)
    {
        try {
            $appointment = $this->bookingService->bookAppointment(
                $request->user()->id,
                $request->generated_slot_id,
                $request->reason,
                $request->additional_comments
            );

            return response()->json([
                'success' => true,
                'message' => 'Appointment booked successfully! Queue #: ' . $appointment->queue_number,
                'appointment' => $appointment->load('service', 'staff'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Show student's appointments.
     */
    public function myAppointments(Request $request)
    {
        $status = $request->get('status', 'all');

        $appointments = Appointment::where('student_id', $request->user()->id)
            ->when($status !== 'all', function ($q) use ($status) {
                if ($status === 'closed') {
                    $q->whereIn('status', ['cancelled', 'cancelled_by_staff', 'rejected']);
                } else {
                    $q->where('status', $status);
                }
            })
            ->with(['service', 'staff'])
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->paginate(15)
            ->appends(['status' => $status]);

        return view('student.my-appointments', compact('appointments', 'status'));
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        if ($appointment->student_id !== $request->user()->id) {
            abort(403);
        }

        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Cannot cancel this appointment.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $this->bookingService->cancelAppointment($appointment, $request->user()->id, $validated['cancellation_reason']);

        return back()->with('success', 'Appointment cancelled successfully.');
    }
}
