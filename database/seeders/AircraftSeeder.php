<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AircraftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layouts = config('aircraft_layouts');

        foreach ($layouts as $registration => $data) {
            \App\Models\Aircraft::updateOrCreate(
                ['registration' => $registration],
                [
                    'type' => $data['type'],
                    'icon' => $data['icon'],
                    'layout' => $data['layout'],
                    'status' => 'active', // Default all to active
                ]
            );
        }
    }
}
