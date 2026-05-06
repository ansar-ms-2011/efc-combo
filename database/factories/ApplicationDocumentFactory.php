<?php

namespace Database\Factories;

use App\Models\ApplicationDocument;
use App\Models\RequiredDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationDocument>
 */
class ApplicationDocumentFactory extends Factory
{
    public function definition(): array
    {
        $paths = [
            'applications/documents/5a21023e-4b9c-4134-905a-1f26567a0ea6.jpg',
            'applications/documents/347e91ac-ccb5-464c-8740-85fc031d69ac.jpg',
            'applications/documents/b0709526-4d84-481a-84c1-a79285fba58b.jpg',
            'applications/documents/f8f0e1db-020b-4e4e-a755-9610fa845109.jpg',
        ];

        $path = $paths[array_rand($paths)];

        return [
            // REQUIRED FK (FIXED)
            'required_document_id' => RequiredDocument::inRandomOrder()->value('id'),

            'upload_method' => 'manual',

            'file_path' => $path,
            'mime_type' => 'image/jpeg',
            'original_name' => basename($path),

            'ac_acr_verified' => false,
            'dc_verified' => false,
        ];
    }
}