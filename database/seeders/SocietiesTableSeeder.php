<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Society;
use App\Models\University;

class SocietiesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure University of Kelaniya exists
        $uok = University::firstOrCreate(
            ['slug' => 'uok'],
            [
                'name' => 'University of Kelaniya',
                'logo_url' => '/images/universities/uok.png', // optional
                'website' => 'https://www.kln.ac.lk/',
            ]
        );

        $societies = [
            [
                'name' => "Information Technology Students' Association",
                'slug' => 'itsa',
                'logo_url' => '/images/societies/itsa.png',
                'description' => 'Community of IT students at UoK',
                'join_link' => 'https://forms.gle/itsa-form-link',
            ],
            [
                'name' => "Computer Science Students' Association",
                'slug' => 'cssa',
                'logo_url' => '/images/societies/cssa.png',
                'description' => 'Community of CS students at UoK',
                'join_link' => 'https://forms.gle/cssa-form-link',
            ],
            [
                'name' => "Entrepreneurship & Technology Students' Association",
                'slug' => 'etsa',
                'logo_url' => '/images/societies/etsa.png',
                'description' => 'Promoting entrepreneurship & innovation',
                'join_link' => 'https://forms.gle/etsa-form-link',
            ],
            [
                'name' => "Free & Open Source Software Community",
                'slug' => 'foss',
                'logo_url' => '/images/societies/foss.png',
                'description' => 'Promoting FOSS culture at UoK',
                'join_link' => 'https://forms.gle/foss-form-link',
            ],
            [
                'name' => "AIESEC University of Kelaniya",
                'slug' => 'aiesec',
                'logo_url' => '/images/societies/aiesec.png',
                'description' => 'Global youth leadership organization',
                'join_link' => 'https://forms.gle/aiesec-form-link',
            ],
        ];

        foreach ($societies as $s) {
            Society::updateOrCreate(
                ['slug' => $s['slug']],
                array_merge($s, ['university_id' => $uok->id]) // ✅ assign UoK
            );
        }
    }
}
