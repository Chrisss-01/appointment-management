<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AvailabilitySlot;
use App\Models\GeneratedSlot;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffAvailabilityDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_deleting_booked_availability_preserves_cancelled_appointment_history(): void
    {
        $staff = User::factory()->staff()->create([
            'is_active' => true,
        ]);
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'department' => 'Nursing',
            'program' => 'BSN',
            'year_level' => '3',
            'student_id' => '2026-0001',
        ]);

        $service = Service::create([
            'name' => 'Medical Consultation',
            'slug' => 'medical-consultation',
            'duration_minutes' => 15,
            'color' => '#1392EC',
            'form_type' => 'medical',
            'is_active' => true,
        ]);

        $availability = AvailabilitySlot::create([
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'slot_duration' => 15,
            'is_active' => true,
        ]);

        $slot = GeneratedSlot::create([
            'availability_slot_id' => $availability->id,
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'date' => $availability->date,
            'start_time' => '08:00:00',
            'end_time' => '08:15:00',
            'status' => 'booked',
        ]);

        $appointment = Appointment::create([
            'student_id' => $student->id,
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'generated_slot_id' => $slot->id,
            'date' => $availability->date,
            'start_time' => '08:00:00',
            'end_time' => '08:15:00',
            'status' => 'pending',
            'queue_number' => 1,
        ]);

        $response = $this->actingAs($staff)->deleteJson(route('staff.availability.destroy', $availability), [
            'cancellation_reason' => 'Clinic schedule changed',
        ]);

        $response->assertOk()->assertJson([
            'success' => true,
            'message' => 'Availability block deleted and related appointments cancelled.',
        ]);

        $appointment = $appointment->fresh();

        $this->assertNotNull($appointment);
        $this->assertSame('cancelled_by_staff', $appointment->status);
        $this->assertSame('Clinic schedule changed', $appointment->cancellation_reason);
        $this->assertNotNull($appointment->cancelled_at);
        $this->assertNull($appointment->generated_slot_id);

        $this->assertDatabaseMissing('generated_slots', [
            'id' => $slot->id,
        ]);
        $this->assertDatabaseMissing('availability_slots', [
            'id' => $availability->id,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'type' => 'appointment_cancelled',
            'data' => json_encode(['appointment_id' => $appointment->id]),
        ]);

        $studentResponse = $this->actingAs($student)->get(route('student.appointments'));

        $studentResponse->assertOk();
        $studentResponse->assertSee('Cancelled by Staff');
        $studentResponse->assertSee('Clinic schedule changed');
        $studentResponse->assertSee($service->name);
    }

    public function test_staff_deleting_unbooked_availability_removes_slots_without_affecting_appointments(): void
    {
        $staff = User::factory()->staff()->create([
            'is_active' => true,
        ]);

        $service = Service::create([
            'name' => 'Dental Consultation',
            'slug' => 'dental-consultation',
            'duration_minutes' => 15,
            'color' => '#10B981',
            'form_type' => 'medical',
            'is_active' => true,
        ]);

        $availability = AvailabilitySlot::create([
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'slot_duration' => 15,
            'is_active' => true,
        ]);

        $slot = GeneratedSlot::create([
            'availability_slot_id' => $availability->id,
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'date' => $availability->date,
            'start_time' => '10:00:00',
            'end_time' => '10:15:00',
            'status' => 'available',
        ]);

        $response = $this->actingAs($staff)->deleteJson(route('staff.availability.destroy', $availability));

        $response->assertOk()->assertJson([
            'success' => true,
            'message' => 'Availability block deleted.',
        ]);

        $this->assertDatabaseMissing('generated_slots', [
            'id' => $slot->id,
        ]);
        $this->assertDatabaseMissing('availability_slots', [
            'id' => $availability->id,
        ]);
        $this->assertDatabaseCount('appointments', 0);
    }
}
