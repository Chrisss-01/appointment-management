<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAvailabilityRequest;
use App\Models\AvailabilitySlot;
use App\Models\GeneratedSlot;
use App\Models\Service;
use App\Services\SlotGenerationService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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

        return view('staff.availability', compact('services'));
    }

    /**
     * Get availability blocks for a specific date (AJAX - panel data).
     */
    public function getByDate(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $now = now();
        $today = $now->toDateString();

        if ($request->date < $today) {
            return response()->json([]);
        }

        $slots = AvailabilitySlot::where('staff_id', $request->user()->id)
            ->whereDate('date', $request->date)
            ->where('is_active', true)
            ->with(['service', 'generatedSlots'])
            ->orderBy('start_time')
            ->get()
            ->filter(function ($slot) use ($now, $today, $request) {
                if ($request->date === $today) {
                    return Carbon::parse($slot->end_time)->gt($now);
                }
                return true;
            })
            ->map(function ($slot) {
                $total = $slot->generatedSlots->count();
                $free = $slot->generatedSlots->where('status', 'available')->count();
                return [
                    'id' => $slot->id,
                    'service_name' => $slot->service->name,
                    'service_color' => $slot->service->color,
                    'start_time' => \Carbon\Carbon::parse($slot->start_time)->format('g:i A'),
                    'end_time' => \Carbon\Carbon::parse($slot->end_time)->format('g:i A'),
                    'total_slots' => $total,
                    'free_slots' => $free,
                    'check_url' => route('staff.availability.check-bookings', $slot),
                    'destroy_url' => route('staff.availability.destroy', $slot),
                ];
            })
            ->values();

        return response()->json($slots);
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
        $now = now();
        $today = $now->toDateString();

        $slots = AvailabilitySlot::where('staff_id', $staffId)
            ->when($serviceId, fn($q) => $q->where('service_id', $serviceId))
            ->where('is_active', true)
            ->whereDate('date', '>=', $today)
            ->with(['generatedSlots', 'service'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->filter(function ($slot) use ($now, $today) {
                if ($slot->date->format('Y-m-d') === $today) {
                    return Carbon::parse($slot->end_time)->gt($now);
                }
                return true;
            })
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
            })
            ->values();

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
     * Upcoming availability list grouped by date (AJAX — List View).
     * Returns only today-onwards; today's past timeslots are excluded.
     */
    public function upcomingList(Request $request)
    {
        $staffId = $request->user()->id;
        $now     = Carbon::now();
        $today   = $now->toDateString();

        $slots = AvailabilitySlot::where('staff_id', $staffId)
            ->whereDate('date', '>=', $today)
            ->where('is_active', true)
            ->with(['service', 'generatedSlots'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->filter(function ($slot) use ($now, $today) {
                // For today: hide slots whose end_time has already passed
                if ($slot->date->toDateString() === $today) {
                    return Carbon::parse($slot->end_time)->gt($now);
                }
                return true;
            })
            ->map(function ($slot) {
                $total   = $slot->generatedSlots->count();
                $booked  = $slot->generatedSlots->where('status', 'booked')->count();
                $free    = $slot->generatedSlots->where('status', 'available')->count();

                return [
                    'id'              => $slot->id,
                    'date'            => $slot->date->format('Y-m-d'),
                    'date_formatted'  => $slot->date->format('F j, Y'),
                    'date_label'      => $slot->date->isToday()    ? 'Today'
                                      : ($slot->date->isTomorrow() ? 'Tomorrow'
                                      : $slot->date->format('l, F j')),
                    'service_id'      => $slot->service_id,
                    'service_name'    => $slot->service->name,
                    'service_color'   => $slot->service->color,
                    'start_time_raw'  => Carbon::parse($slot->start_time)->format('H:i'),
                    'end_time_raw'    => Carbon::parse($slot->end_time)->format('H:i'),
                    'start_time'      => Carbon::parse($slot->start_time)->format('g:i A'),
                    'end_time'        => Carbon::parse($slot->end_time)->format('g:i A'),
                    'slot_duration'   => $slot->slot_duration,
                    'total_slots'     => $total,
                    'booked_slots'    => $booked,
                    'free_slots'      => $free,
                    'has_bookings'    => $booked > 0,
                    'check_url'       => route('staff.availability.check-bookings', $slot),
                    'destroy_url'     => route('staff.availability.destroy', $slot),
                    'update_url'      => route('staff.availability.update', $slot),
                ];
            })
            ->values();

        // Group by date for the frontend
        $grouped = $slots->groupBy('date')->map(fn($items) => [
            'date'           => $items->first()['date'],
            'date_formatted' => $items->first()['date_formatted'],
            'date_label'     => $items->first()['date_label'],
            'slots'          => $items->values(),
        ])->values();

        return response()->json($grouped);
    }

    /**
     * Update an availability block (only allowed when no bookings exist
     * for schedule-sensitive fields; service is always editable).
     */
    public function update(Request $request, AvailabilitySlot $availabilitySlot)
    {
        if ($availabilitySlot->staff_id !== $request->user()->id) {
            abort(403);
        }

        $hasBookings = $availabilitySlot->generatedSlots()->where('status', 'booked')->exists();

        $rules = ['service_id' => 'sometimes|exists:services,id'];

        if (!$hasBookings) {
            $rules += [
                'start_time'    => 'sometimes|date_format:H:i',
                'end_time'      => 'sometimes|date_format:H:i|after:start_time',
                'slot_duration' => 'sometimes|integer|min:5|max:60',
            ];
        }

        $validated = $request->validate($rules);

        if ($hasBookings) {
            unset($validated['start_time'], $validated['end_time'], $validated['slot_duration']);
        }

        $availabilitySlot->update($validated);

        return response()->json(['success' => true, 'message' => 'Availability updated.']);
    }


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
            'staff_id' => $request->user()->id,
            'service_id' => $validated['service_id'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'slot_duration' => $validated['slot_duration'] ?? 15,
            'is_active' => true,
        ]);

        $generatedSlots = $this->slotService->generateSlots($availabilitySlot);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Availability created with {$generatedSlots->count()} time slots.",
            ]);
        }

        return back()->with('success', "Availability created with {$generatedSlots->count()} time slots.");
    }

    /**
     * Bulk-create availability — supports 'specific' dates or 'recurring' weekday patterns.
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'mode'          => 'required|in:specific,recurring',
            'service_id'    => 'required|exists:services,id',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'slot_duration' => 'nullable|integer|min:5|max:60',
            // specific
            'dates'         => 'required_if:mode,specific|array|min:1',
            'dates.*'       => 'date|after_or_equal:today',
            // recurring
            'weekdays'      => 'required_if:mode,recurring|array|min:1',
            'weekdays.*'    => 'integer|min:0|max:6',
            'start_date'    => 'required_if:mode,recurring|date|after_or_equal:today',
            'end_date'      => 'required_if:mode,recurring|date|after_or_equal:start_date',
        ]);

        $staffId      = $request->user()->id;
        $serviceId    = $request->service_id;
        $startTime    = $request->start_time;
        $endTime      = $request->end_time;
        $slotDuration = $request->slot_duration ?? 15;

        // Build the list of dates to process
        $dates = [];

        if ($request->mode === 'specific') {
            $dates = array_unique($request->dates);
        } else {
            // Recurring: walk every day in range and keep those matching selected weekdays
            // weekdays: 0=Sun, 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat (Carbon convention)
            $weekdays = array_map('intval', $request->weekdays);
            $period   = CarbonPeriod::create($request->start_date, $request->end_date);

            foreach ($period as $day) {
                if (in_array($day->dayOfWeek, $weekdays, true)) {
                    $dates[] = $day->toDateString();
                }
            }
        }

        if (empty($dates)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid dates found for the selected criteria.',
            ], 422);
        }

        $created  = 0;
        $skipped  = 0;
        $totalSlots = 0;

        foreach ($dates as $date) {
            // Skip if an overlapping block already exists for this service+date
            $overlap = AvailabilitySlot::where('staff_id', $staffId)
                ->where('service_id', $serviceId)
                ->whereDate('date', $date)
                ->where('is_active', true)
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->exists();

            if ($overlap) {
                $skipped++;
                continue;
            }

            $slot = AvailabilitySlot::create([
                'staff_id'      => $staffId,
                'service_id'    => $serviceId,
                'date'          => $date,
                'start_time'    => $startTime,
                'end_time'      => $endTime,
                'slot_duration' => $slotDuration,
                'is_active'     => true,
            ]);

            $generated   = $this->slotService->generateSlots($slot);
            $totalSlots += $generated->count();
            $created++;
        }

        $message = "Created {$created} availability block(s) with {$totalSlots} total time slots.";
        if ($skipped > 0) {
            $message .= " {$skipped} date(s) skipped (overlap detected).";
        }


        return response()->json(['success' => true, 'message' => $message, 'created' => $created, 'skipped' => $skipped]);
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

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Availability block deleted and related appointments cancelled.']);
            }
            return back()->with('success', 'Availability block deleted and related appointments cancelled.');
        }

        $this->slotService->deleteSlots($availabilitySlot);
        $availabilitySlot->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Availability block deleted.']);
        }
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
