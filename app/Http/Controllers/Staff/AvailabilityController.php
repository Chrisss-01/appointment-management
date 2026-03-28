<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAvailabilityRequest;
use App\Models\AvailabilitySlot;
use App\Models\GeneratedSlot;
use App\Models\Service;
use App\Services\SlotGenerationService;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function __construct(
        private SlotGenerationService $slotService
    ) {}

    /**
     * Show availability management page.
     */
    public function index(Request $request)
    {
        $services = Service::active()
            ->where('slug', '!=', 'medical-certificate-request')
            ->get();
        $availabilitySlots = AvailabilitySlot::where('staff_id', $request->user()->id)
            ->where('is_active', true)
            ->where('date', '>=', now()->toDateString())
            ->with(['service', 'generatedSlots'])
            ->orderBy('date')
            ->get();

        return view('staff.availability', compact('services', 'availabilitySlots'));
    }

    /**
     * Get calendar data for a specific service (AJAX).
     */
    public function calendarData(Request $request)
    {
        $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'month' => 'nullable|date_format:Y-m',
        ]);

        $staffId = $request->user()->id;
        $serviceId = $request->service_id;

        $slots = AvailabilitySlot::where('staff_id', $staffId)
            ->when($serviceId, fn($q) => $q->where('service_id', $serviceId))
            ->where('is_active', true)
            ->with(['generatedSlots', 'service'])
            ->get()
            ->map(function ($slot) {
                $totalGenerated = $slot->generatedSlots->count();
                $booked = $slot->generatedSlots->where('status', 'booked')->count();
                $available = $slot->generatedSlots->where('status', 'available')->count();

                return [
                    'id' => $slot->id,
                    'date' => $slot->date->format('Y-m-d'),
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'slot_duration' => $slot->slot_duration,
                    'total_slots' => $totalGenerated,
                    'booked_slots' => $booked,
                    'available_slots' => $available,
                    'service_color' => $slot->service->color,
                    'service_name' => $slot->service->name,
                ];
            });

        return response()->json($slots);
    }

    /**
     * Get all services with color for calendar display.
     */
    public function servicesWithAvailability(Request $request)
    {
        $staffId = $request->user()->id;

        $services = Service::active()->get()->map(function ($service) use ($staffId) {
            $datesWithAvailability = AvailabilitySlot::where('staff_id', $staffId)
                ->where('service_id', $service->id)
                ->where('is_active', true)
                ->where('date', '>=', now()->toDateString())
                ->pluck('date')
                ->map(fn($d) => $d->format('Y-m-d'))
                ->unique()
                ->values();

            return [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'color' => $service->color,
                'dates' => $datesWithAvailability,
            ];
        });

        return response()->json($services);
    }

    /**
     * Store a new availability block and auto-generate slots.
     */
    public function store(StoreAvailabilityRequest $request)
    {
        $validated = $request->validated();

        // Check for overlapping availability
        $overlap = AvailabilitySlot::where('staff_id', $request->user()->id)
            ->where('service_id', $validated['service_id'])
            ->where('date', $validated['date'])
            ->where('is_active', true)
            ->where(function ($q) use ($validated) {
                $q->where(function ($q2) use ($validated) {
                    $q2->where('start_time', '<', $validated['end_time'])
                        ->where('end_time', '>', $validated['start_time']);
                });
            })
            ->exists();

        if ($overlap) {
            return back()->with('error', 'This time block overlaps with an existing availability.');
        }

        $availabilitySlot = AvailabilitySlot::create([
            'staff_id' => $request->user()->id, // staff_id is the foreign key in DB
            'service_id' => $validated['service_id'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'slot_duration' => $validated['slot_duration'] ?? 15,
            'is_active' => true,
        ]);

        // Auto-generate individual slots
        $generatedSlots = $this->slotService->generateSlots($availabilitySlot);

        return back()->with('success', "Availability created with {$generatedSlots->count()} time slots.");
    }

    /**
     * Delete an availability block.
     */
    public function destroy(Request $request, AvailabilitySlot $availabilitySlot)
    {
        if ($availabilitySlot->staff_id !== $request->user()->id) {
            abort(403);
        }

        $hasBookings = $this->slotService->hasBookedSlots($availabilitySlot);

        if ($hasBookings) {
            $request->validate([
                'cancellation_reason' => 'required|string|max:1000',
            ]);

            $this->slotService->forceDeleteSlots($availabilitySlot, $request->cancellation_reason);
            $availabilitySlot->delete();

            return back()->with('success', 'Availability block deleted and related appointments cancelled.');
        }

        $this->slotService->deleteSlots($availabilitySlot);
        $availabilitySlot->delete();

        return back()->with('success', 'Availability block deleted.');
    }

    /**
     * Check if an availability block has booked appointments (AJAX).
     */
    public function checkBookings(Request $request, AvailabilitySlot $availabilitySlot)
    {
        if ($availabilitySlot->staff_id !== $request->user()->id) {
            abort(403);
        }

        $bookedCount = $availabilitySlot->generatedSlots()
            ->where('status', 'booked')
            ->count();

        return response()->json([
            'has_bookings' => $bookedCount > 0,
            'booked_count' => $bookedCount,
        ]);
    }

    /**
     * Get generated slots for a specific availability block (AJAX).
     */
    public function slots(Request $request, AvailabilitySlot $availabilitySlot)
    {
        if ($availabilitySlot->staff_id !== $request->user()->id) {
            abort(403);
        }

        $slots = $availabilitySlot->generatedSlots()
            ->with('appointment.student')
            ->orderBy('start_time')
            ->get();

        return response()->json($slots);
    }
}
