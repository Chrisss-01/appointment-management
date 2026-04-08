<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Seed the default clinic services.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Medical Consultation',
                'slug' => 'medical-consultation',
                'description' => 'General medical consultation for common illnesses, health concerns, and physical examination.',
                'duration_minutes' => 15,
                'color' => '#1392EC', // blue
                'icon' => 'stethoscope',
                'is_active' => true,
                'form_type' => 'standard_consultation',
            ],
            [
                'name' => 'Dental Consultation',
                'slug' => 'dental-consultation',
                'description' => 'Dental check-up, consultation, and basic dental procedures.',
                'duration_minutes' => 15,
                'color' => '#A855F7', // purple
                'icon' => 'dentistry',
                'is_active' => true,
                'form_type' => 'standard_consultation',
            ],
            [
                'name' => 'Vision Screening',
                'slug' => 'vision-screening',
                'description' => 'Basic vision and eye health screening.',
                'duration_minutes' => 15,
                'color' => '#10B981', // emerald
                'icon' => 'visibility',
                'is_active' => true,
                'form_type' => 'vision_screening',
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['slug' => $service['slug']],
                $service
            );
        }
    }
}
