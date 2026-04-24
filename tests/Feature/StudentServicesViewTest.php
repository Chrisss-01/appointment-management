<?php

namespace Tests\Feature;

use App\Models\AvailabilitySlot;
use App\Models\GeneratedSlot;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StudentServicesViewTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_student_services_page_only_marks_services_with_bookable_slots_as_available(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-24 21:05:00'));

        $student = $this->createStudent();
        $staff = $this->createStaff();

        $futureService = $this->createService('General Checkup', 'general-checkup');
        $pastTodayService = $this->createService('Dental Consultation', 'dental-consultation');
        $leadWindowService = $this->createService('Vision Screening', 'vision-screening');
        $noSlotsService = $this->createService('Nutrition Advice', 'nutrition-advice');
        $deletedSlotService = $this->createService('Follow-up Care', 'follow-up-care');

        $this->createGeneratedSlot($staff, $futureService, now()->addDay()->toDateString(), '09:00:00', '09:15:00');
        $this->createGeneratedSlot($staff, $pastTodayService, now()->toDateString(), '19:00:00', '19:15:00');
        $this->createGeneratedSlot($staff, $leadWindowService, now()->toDateString(), '21:20:00', '21:35:00');

        $deletedSlot = $this->createGeneratedSlot($staff, $deletedSlotService, now()->addDay()->toDateString(), '10:00:00', '10:15:00');
        $deletedSlot->delete();

        $response = $this->actingAs($student)->get(route('student.services'));
        $html = $response->getContent();

        $response->assertOk();
        $response->assertSee(route('student.services.show', $futureService), false);
        $response->assertDontSee(route('student.services.show', $pastTodayService), false);
        $response->assertDontSee(route('student.services.show', $leadWindowService), false);
        $response->assertDontSee(route('student.services.show', $noSlotsService), false);
        $response->assertDontSee(route('student.services.show', $deletedSlotService), false);
        $this->assertStringContainsString($futureService->name, $html);
        $this->assertStringContainsString($pastTodayService->name, $html);
        $this->assertStringContainsString($leadWindowService->name, $html);
        $this->assertStringContainsString($noSlotsService->name, $html);
        $this->assertStringContainsString($deletedSlotService->name, $html);
        $response->assertSee('Available');
        $response->assertSee('Unavailable');
        $response->assertSee('Currently unavailable');
    }

    public function test_student_services_page_marks_late_night_same_day_slots_as_unavailable(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-24 23:44:00'));

        $student = $this->createStudent();
        $staff = $this->createStaff();
        $service = $this->createService('Dental Consultation', 'dental-consultation');

        $this->createGeneratedSlot($staff, $service, now()->toDateString(), '19:00:00', '19:15:00');
        $this->createGeneratedSlot($staff, $service, now()->toDateString(), '20:00:00', '20:15:00');
        $this->createGeneratedSlot($staff, $service, now()->toDateString(), '20:45:00', '21:00:00');

        $response = $this->actingAs($student)->get(route('student.services'));

        $response->assertOk();
        $response->assertDontSee(route('student.services.show', $service), false);
        $response->assertSee($service->name);
        $response->assertSee('Unavailable');
        $response->assertSee('Currently unavailable');
    }

    private function createStudent(): User
    {
        return User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'department' => 'college',
            'program' => 'BSIT',
            'year_level' => '3rd-year',
            'student_id' => '20260001',
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
