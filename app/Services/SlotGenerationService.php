<?php

namespace App\Services;

use App\Models\AvailabilitySlot;
use App\Models\GeneratedSlot;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SlotGenerationService
{
    /**
     * Generate individual time slots from an availability block.
     *
     * Example: 8:00–10:00 with 15-min duration = 8 slots:
     *   8:00–8:15, 8:15–8:30, ..., 9:45–10:00
     *
     * @param  AvailabilitySlot  $availabilitySlot
     * @return Collection<int, GeneratedSlot>
     */
    public function generateSlots(AvailabilitySlot $availabilitySlot): Collection
    {
        $slots = collect();

        $start = Carbon::parse($availabilitySlot->date->format('Y-m-d') . ' ' . $availabilitySlot->start_time);
        $end = Carbon::parse($availabilitySlot->date->format('Y-m-d') . ' ' . $availabilitySlot->end_time);
        $duration = $availabilitySlot->slot_duration;

        while ($start->copy()->addMinutes($duration)->lte($end)) {
            $slotEnd = $start->copy()->addMinutes($duration);

            $slot = GeneratedSlot::create([
                'availability_slot_id' => $availabilitySlot->id,
                'staff_id' => $availabilitySlot->staff_id,
                'service_id' => $availabilitySlot->service_id,
                'date' => $availabilitySlot->date,
                'start_time' => $start->format('H:i:s'),
                'end_time' => $slotEnd->format('H:i:s'),
                'status' => 'available',
            ]);

            $slots->push($slot);
            $start = $slotEnd;
        }

        return $slots;
    }

    /**
     * Regenerate slots for an availability block.
     * Deletes existing unbooked slots and recreates them.
     */
    public function regenerateSlots(AvailabilitySlot $availabilitySlot): Collection
    {
        // Only delete slots that aren't booked
        $availabilitySlot->generatedSlots()
            ->where('status', '!=', 'booked')
            ->delete();

        return $this->generateSlots($availabilitySlot);
    }

    /**
     * Delete all generated slots for an availability block.
     * Returns false if any slots are booked (cannot delete).
     */
    public function deleteSlots(AvailabilitySlot $availabilitySlot): bool
    {
        $hasBookedSlots = $availabilitySlot->generatedSlots()
            ->where('status', 'booked')
            ->exists();

        if ($hasBookedSlots) {
            return false;
        }

        $availabilitySlot->generatedSlots()->delete();
        return true;
    }

    /**
     * Force-delete all generated slots, cancelling any booked appointments.
     */
    public function forceDeleteSlots(AvailabilitySlot $availabilitySlot, string $cancellationReason): void
    {
        DB::transaction(function () use ($availabilitySlot, $cancellationReason) {
            $bookedSlots = $availabilitySlot->generatedSlots()
                ->where('status', 'booked')
                ->with('appointment')
                ->get();

            foreach ($bookedSlots as $slot) {
                $appointment = $slot->appointment;

                if ($appointment && in_array($appointment->status, ['pending', 'approved'])) {
                    $appointment->cancelByStaff($cancellationReason);

                    Notification::send(
                        $appointment->student_id,
                        'appointment_cancelled',
                        'Appointment Cancelled by Staff',
                        "Your appointment on {$appointment->date->format('M d, Y')} at {$appointment->start_time} has been cancelled by staff. Reason: {$cancellationReason}",
                        ['appointment_id' => $appointment->id]
                    );

                    $appointment->update([
                        'generated_slot_id' => null,
                    ]);
                }
            }

            $availabilitySlot->generatedSlots()->delete();
        });
    }

    /**
     * Check if an availability block has any booked slots.
     */
    public function hasBookedSlots(AvailabilitySlot $availabilitySlot): bool
    {
        return $availabilitySlot->generatedSlots()
            ->where('status', 'booked')
            ->exists();
    }
}
