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
                'color' => '#1392EC',
                'icon' => 'clinical_notes',
                'documents' => [
                    ['name' => 'Valid School ID', 'description' => 'Front and back photo', 'is_required' => true],
                    ['name' => 'Medical History Form', 'description' => 'Filled out clinic form', 'is_required' => true],
                ],
                'purposes' => ['Academic Requirement', 'Sports Clearance', 'OJT/Internship', 'Employment', 'Other'],
            ],
            [
                'name' => 'Dental Certificate',
                'slug' => 'dental-certificate',
                'description' => 'Dental health certificate and clearance.',
                'color' => '#A855F7',
                'icon' => 'dentistry',
                'documents' => [
                    ['name' => 'Valid School ID', 'description' => 'Front and back photo', 'is_required' => true],
                    ['name' => 'Dental Record', 'description' => 'Previous dental records if available', 'is_required' => false],
                ],
                'purposes' => ['Academic Requirement', 'Employment', 'Other'],
            ],
            [
                'name' => 'Fit to Work Certificate',
                'slug' => 'fit-to-work',
                'description' => 'Certificate confirming fitness for work or on-the-job training.',
                'color' => '#10B981',
                'icon' => 'verified_user',
                'documents' => [
                    ['name' => 'Valid School ID', 'description' => 'Front and back photo', 'is_required' => true],
                    ['name' => 'Company/OJT Endorsement Letter', 'description' => 'From the company or department', 'is_required' => true],
                ],
                'purposes' => ['OJT/Internship', 'Employment', 'Other'],
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
