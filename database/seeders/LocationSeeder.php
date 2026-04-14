<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Estante A1',
                'code' => 'SHELF-A1',
                'type' => 'shelf',
                'capacity' => 500,
            ],
            [
                'name' => 'Estante A2',
                'code' => 'SHELF-A2',
                'type' => 'shelf',
                'capacity' => 500,
            ],
            [
                'name' => 'Refrigerador B1',
                'code' => 'REFRIG-B1',
                'type' => 'refrigerator',
                'capacity' => 300,
                'min_temp' => 2,
                'max_temp' => 8,
            ],
            [
                'name' => 'Congelador C1',
                'code' => 'FREEZE-C1',
                'type' => 'freezer',
                'capacity' => 200,
                'min_temp' => -20,
                'max_temp' => -15,
            ],
            [
                'name' => 'Bodega General',
                'code' => 'WARE-MAIN',
                'type' => 'warehouse',
                'capacity' => 1000,
            ],
        ];

        foreach ($locations as $location) {
            Location::firstOrCreate(
                ['code' => $location['code']],
                $location
            );
        }
    }
}
