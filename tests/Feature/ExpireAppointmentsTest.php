<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\GeneratedSlot;
use App\Models\Service;
use App\Models\User;
use App\Models\AvailabilitySlot;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpireAppointmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_expires_pending_appointments_in_the_past()
    {
        // Setup users
        $student = User::factory()->create(['role' => 'student']);
        $staff = User::factory()->create(['role' => 'staff']);
        
        // Setup service manually
        $service = Service::create([
            'name' => 'Medical Consultation',
            'slug' => 'medical-consultation',
            'duration_minutes' => 15,
            'color' => '#1392EC',
            'form_type' => 'medical',
            'is_active' => true,
        ]);

        // Setup availability slot for yesterday
        $availPast = AvailabilitySlot::create([
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'date' => Carbon::yesterday()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'slot_duration' => 15,
            'is_active' => true,
        ]);

        // Setup past slot manually
        $slot1 = GeneratedSlot::create([
            'availability_slot_id' => $availPast->id,
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'date' => Carbon::yesterday()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '08:15:00',
            'status' => 'booked',
        ]);

        $pastAppointment = Appointment::create([
            'student_id' => $student->id,
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'generated_slot_id' => $slot1->id,
            'date' => Carbon::yesterday()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '08:15:00',
            'status' => 'pending',
        ]);

        // Setup availability slot for tomorrow
        $availFuture = AvailabilitySlot::create([
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'slot_duration' => 15,
            'is_active' => true,
        ]);

        // Setup future slot manually
        $slot2 = GeneratedSlot::create([
            'availability_slot_id' => $availFuture->id,
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '08:15:00',
            'status' => 'booked',
        ]);

        $futureAppointment = Appointment::create([
            'student_id' => $student->id,
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'generated_slot_id' => $slot2->id,
            'date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '08:15:00',
            'status' => 'pending',
        ]);

        // Run the command
        $this->artisan('app:expire-appointments')->assertExitCode(0);

        // Assert
        $this->assertEquals('expired', $pastAppointment->fresh()->status);
        $this->assertEquals('Not approved by clinic', $pastAppointment->fresh()->expiry_reason);
        $this->assertEquals('booked', $slot1->fresh()->status); // Slot remains booked as per refactor

        // Assert notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'type' => 'appointment_expired',
        ]);

        $this->assertEquals('pending', $futureAppointment->fresh()->status);
        $this->assertEquals('booked', $slot2->fresh()->status);
    }
}
