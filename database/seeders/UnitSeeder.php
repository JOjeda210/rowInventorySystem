<?php

namespace Database\Seeders;

use App\Models\UnitOfMeasure;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Kilogramo', 'abbreviation' => 'kg'],
            ['name' => 'Gramo', 'abbreviation' => 'g'],
            ['name' => 'Litro', 'abbreviation' => 'L'],
            ['name' => 'Mililitro', 'abbreviation' => 'mL'],
            ['name' => 'Pieza', 'abbreviation' => 'pz'],
            ['name' => 'Saco', 'abbreviation' => 'saco'],
            ['name' => 'Caja', 'abbreviation' => 'caja'],
            ['name' => 'Docena', 'abbreviation' => 'doc'],
        ];

        foreach ($units as $unit) {
            UnitOfMeasure::firstOrCreate($unit);
        }
    }
}
