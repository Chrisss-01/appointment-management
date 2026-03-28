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
                'is_active' => true,
            ],
            [
                'name' => 'Dental Consultation',
                'slug' => 'dental-consultation',
                'description' => 'Dental check-up, consultation, and basic dental procedures.',
                'duration_minutes' => 15,
                'color' => '#A855F7', // purple
                'is_active' => true,
            ],
            [
                'name' => 'Medical Certificate Request',
                'slug' => 'medical-certificate-request',
                'description' => 'Request for medical certificates for academic or personal purposes.',
                'duration_minutes' => 15,
                'color' => '#F59E0B', // amber
                'is_active' => true,
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
