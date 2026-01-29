<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Airline;

class AirlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $airlines = [
            ['id' => 1, 'name' => 'Garuda Indonesia', 'code' => 'GA', 'icon' => ''],
            ['id' => 2, 'name' => 'CITILINK', 'code' => 'QG', 'icon' => ''],
        ];

        foreach ($airlines as $airline) {
            Airline::updateOrCreate(
                ['id' => $airline['id']],
                $airline
            );
        }
    }
}
