<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;  // <-- Add this line to import the DB facade

class SocietiesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('societies')->insert([
            [
                'name' => 'Legion-society',  // Correct society name used in your code
                'logo_url' => 'storage/societies/legion.png',  // Path to Legion logo
            ],
            [
                'name' => 'ITSA-society',  // Correct society name used in your code
                'logo_url' => 'storage/societies/itsa.png',  // Path to ITSA logo
            ],
            // Add more societies here if needed...
        ]);
    }
}
