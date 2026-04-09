<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\GeneratedSlot;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class BookingService
{
    /**
     * Book an appointment for a student.
     *
     * @param  int  $studentId
     * @param  int  $generatedSlotId
     * @param  string|null  $reason
     * @return Appointment
     *
     * @throws \Exception
     */
    public function bookAppointment(int $studentId, int $generatedSlotId, ?string $reason = null, ?string $additionalComments = null): Appointment
    {
        return DB::transaction(function () use ($studentId, $generatedSlotId, $reason, $additionalComments) {
            $slot = GeneratedSlot::lockForUpdate()->findOrFail($generatedSlotId);

            if (!$slot->isAvailable()) {
                throw new \Exception('This time slot is no longer available.');
            }

            // Enforce minimum 30-minute booking lead time
            $slotDateTime = \Carbon\Carbon::parse($slot->date->format('Y-m-d') . ' ' . $slot->start_time);
            if ($slotDateTime->diffInMinutes(now(), false) > -30) {
                throw new \Exception('This time slot cannot be booked. Appointments must be made at least 30 minutes in advance.');
            }

            // Check for conflicting appointments
            $hasConflict = Appointment::where('student_id', $studentId)
                ->where('date', $slot->date)
                ->where('start_time', $slot->start_time)
                ->whereIn('status', ['pending', 'approved'])
                ->exists();

            if ($hasConflict) {
                throw new \Exception('You already have an appointment at this time.');
            }

            // Generate queue number for the day + service
            $queueNumber = $this->getNextQueueNumber($slot->date->format('Y-m-d'), $slot->service_id);

            // Mark slot as booked
            $slot->markAsBooked();

            // Create appointment
            $appointment = Appointment::create([
                'student_id' => $studentId,
                'staff_id' => $slot->staff_id,
                'service_id' => $slot->service_id,
                'generated_slot_id' => $slot->id,
                'date' => $slot->date,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'status' => 'pending',
                'reason' => $reason,
                'additional_comments' => $additionalComments,
                'queue_number' => $queueNumber,
            ]);

            // Send notification to nurse
            Notification::send(
                $slot->staff_id,
                'appointment_request',
                'New Appointment Request',
                "A student has requested an appointment for {$slot->service->name} on {$slot->date->format('M d, Y')} at {$slot->start_time}.",
                ['appointment_id' => $appointment->id]
            );

            // Send confirmation to student
            Notification::send(
                $studentId,
                'appointment_booked',
                'Appointment Booked',
                "Your appointment for {$slot->service->name} has been submitted. Queue #: {$queueNumber}. Please wait for approval.",
                ['appointment_id' => $appointment->id]
            );

            return $appointment;
        });
    }

    /**
     * Cancel an appointment and free up the slot.
     */
    public function cancelAppointment(Appointment $appointment, int $userId, ?string $reason = null): void
    {
        DB::transaction(function () use ($appointment, $userId, $reason) {
            $appointment->cancel($reason);

            // Notify the other party
            $isStudent = $userId === $appointment->student_id;
            $notifyUserId = $isStudent ? $appointment->staff_id : $appointment->student_id;
            $cancelledBy = $isStudent ? 'student' : 'staff';

            Notification::send(
                $notifyUserId,
                'appointment_cancelled',
                'Appointment Cancelled',
                "An appointment on {$appointment->date->format('M d, Y')} at {$appointment->start_time} has been cancelled by the {$cancelledBy}.",
                ['appointment_id' => $appointment->id]
            );
        });
    }

    /**
     * Get next queue number for a service on a specific date.
     */
    private function getNextQueueNumber(string $date, int $serviceId): int
    {
        $maxQueue = Appointment::where('date', $date)
            ->where('service_id', $serviceId)
            ->max('queue_number');

        return ($maxQueue ?? 0) + 1;
    }
}
