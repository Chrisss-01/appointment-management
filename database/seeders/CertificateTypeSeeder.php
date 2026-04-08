<?php

namespace Database\Seeders;

use App\Models\CertificateType;
use Illuminate\Database\Seeder;

class CertificateTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Medical Certificate',
                'slug' => 'medical-certificate',
                'description' => 'General medical certificate for academic or employment purposes.',
                'color' => '#F59E0B', // Amber
                'icon' => 'clinical_notes',
                'documents' => [
                    ['name' => 'Valid School ID', 'description' => 'Front and back photo', 'is_required' => true],
                    ['name' => 'Medical History Form', 'description' => 'Filled out clinic form', 'is_required' => true],
                ],
                'purposes' => ['Academic Requirement', 'Sports Clearance', 'OJT/Internship', 'Employment', 'Other'],
            ],
        ];

        foreach ($types as $typeData) {
            $documents = $typeData['documents'];
            $purposes = $typeData['purposes'];
            unset($typeData['documents'], $typeData['purposes']);

            $type = CertificateType::updateOrCreate(
                ['slug' => $typeData['slug']],
                $typeData
            );

            foreach ($documents as $doc) {
                $type->requiredDocuments()->updateOrCreate(
                    ['name' => $doc['name']],
                    $doc
                );
            }

            foreach ($purposes as $label) {
                $type->purposePresets()->updateOrCreate(
                    ['label' => $label]
                );
            }
        }
    }
}
