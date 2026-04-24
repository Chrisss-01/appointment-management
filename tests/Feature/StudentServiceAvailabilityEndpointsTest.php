<?php

namespace Tests\Feature;

use App\Models\AvailabilitySlot;
use App\Models\GeneratedSlot;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StudentServiceAvailabilityEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_available_dates_only_include_dates_with_bookable_slots(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-24 21:05:00'));

        $student = $this->createStudent();
        $staff = $this->createStaff();
        $service = $this->createService('Dental Consultation', 'dental-consultation');

        $this->createGeneratedSlot($staff, $service, now()->toDateString(), '19:00:00', '19:15:00');
        $this->createGeneratedSlot($staff, $service, now()->toDateString(), '21:20:00', '21:35:00');
        $this->createGeneratedSlot($staff, $service, now()->toDateString(), '22:00:00', '22:15:00');
        $this->createGeneratedSlot($staff, $service, now()->addDay()->toDateString(), '09:00:00', '09:15:00');

        $response = $this->actingAs($student)->getJson(route('student.services.available-dates', $service));

        $response->assertOk()->assertExactJson([
            [
                'date' => '2026-04-24',
                'slot_count' => 1,
            ],
            [
                'date' => '2026-04-25',
                'slot_count' => 1,
            ],
        ]);
    }

    public function test_available_slots_excludes_past_and_within_lead_time_slots_for_today(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-24 21:05:00'));

        $student = $this->createStudent();
        $staff = $this->createStaff();
        $service = $this->createService('Vision Screening', 'vision-screening');

        $this->createGeneratedSlot($staff, $service, now()->toDateString(), '19:00:00', '19:15:00');
        $this->createGeneratedSlot($staff, $service, now()->toDateString(), '21:20:00', '21:35:00');
        $keptSlot = $this->createGeneratedSlot($staff, $service, now()->toDateString(), '22:00:00', '22:15:00');

        $response = $this->actingAs($student)->getJson(route('student.services.available-slots', [
            'service' => $service,
            'date' => now()->toDateString(),
        ]));

        $response->assertOk()->assertExactJson([
            [
                'id' => $keptSlot->id,
                'start_time' => '22:00:00',
                'end_time' => '22:15:00',
                'staff_name' => $staff->name,
            ],
        ]);
    }

    public function test_late_night_same_day_slots_do_not_appear_as_bookable(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-24 23:44:00'));

        $student = $this->createStudent();
        $staff = $this->createStaff();
        $service = $this->createService('Dental Consultation', 'dental-consultation');

        $this->createGeneratedSlot($staff, $service, now()->toDateString(), '19:00:00', '19:15:00');
        $this->createGeneratedSlot($staff, $service, now()->toDateString(), '20:00:00', '20:15:00');
        $this->createGeneratedSlot($staff, $service, now()->toDateString(), '20:45:00', '21:00:00');

        $this->assertFalse($service->isAvailable());

        $datesResponse = $this->actingAs($student)->getJson(route('student.services.available-dates', $service));
        $datesResponse->assertOk()->assertExactJson([]);

        $slotsResponse = $this->actingAs($student)->getJson(route('student.services.available-slots', [
            'service' => $service,
            'date' => now()->toDateString(),
        ]));

        $slotsResponse->assertOk()->assertExactJson([]);
    }

    private function createStudent(): User
    {
        return User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'department' => 'college',
            'program' => 'BSIT',
            'year_level' => '3rd-year',
            'student_id' => fake()->unique()->numerify('2026####'),
        ]);
    }

    private function createStaff(): User
    {
        return User::factory()->create([
            'role' => 'staff',
            'staff_type' => 'nurse',
            'is_active' => true,
        ]);
    }

    private function createService(string $name, string $slug): Service
    {
        return Service::create([
            'name' => $name,
            'slug' => $slug,
            'description' => "{$name} service.",
            'duration_minutes' => 15,
            'color' => '#1392EC',
            'icon' => 'medical_services',
            'is_active' => true,
        ]);
    }

    private function createGeneratedSlot(User $staff, Service $service, string $date, string $startTime, string $endTime): GeneratedSlot
    {
        $availabilitySlot = AvailabilitySlot::create([
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'slot_duration' => 15,
            'is_active' => true,
        ]);

        return GeneratedSlot::create([
            'availability_slot_id' => $availabilitySlot->id,
            'staff_id' => $staff->id,
            'service_id' => $service->id,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'available',
        ]);
    }
}
