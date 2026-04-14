<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Harina y Cereales',
                'description' => 'Productos de harina, trigo, arroz y otros cereales',
                'is_perishable' => false,
            ],
            [
                'name' => 'Lacteos',
                'description' => 'Leche, queso, mantequilla y derivados lacteos',
                'is_perishable' => true,
            ],
            [
                'name' => 'Aceites y Grasas',
                'description' => 'Aceites vegetales y grasas para cocina',
                'is_perishable' => false,
            ],
            [
                'name' => 'Azucares y Endulzantes',
                'description' => 'Azucar, miel, edulcorantes y similares',
                'is_perishable' => false,
            ],
            [
                'name' => 'Condimentos y Especias',
                'description' => 'Sal, especias, condimentos y saborizantes',
                'is_perishable' => false,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
