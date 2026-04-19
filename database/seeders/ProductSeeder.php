<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $kg = UnitOfMeasure::where('abbreviation', 'kg')->first();
        $L = UnitOfMeasure::where('abbreviation', 'L')->first();
        $pz = UnitOfMeasure::where('abbreviation', 'pz')->first();
        $saco = UnitOfMeasure::where('abbreviation', 'saco')->first();

        $harina = Category::where('name', 'Harina y Cereales')->first();
        $lacteos = Category::where('name', 'Lacteos')->first();
        $aceites = Category::where('name', 'Aceites y Grasas')->first();
        $azucar = Category::where('name', 'Azucares y Endulzantes')->first();
        $condimentos = Category::where('name', 'Condimentos y Especias')->first();

        $bodega = Location::where('code', 'WARE-MAIN')->first();
        $refrig = Location::where('code', 'REFRIG-B1')->first();
        $shelf = Location::where('code', 'SHELF-A1')->first();

        $supplier = Supplier::first();

        $products = [
            [
                'code' => 'PROD-001',
                'name' => 'Harina de Trigo',
                'category_id' => $harina?->id,
                'unit_id' => $kg?->id,
                'min_stock' => 100,
                'max_stock' => 500,
                'location_id' => $bodega?->id,
                'preferred_supplier_id' => $supplier?->id,
                'shelf_life_days' => 365,
                'unit_cost' => 12.50,
            ],
            [
                'code' => 'PROD-002',
                'name' => 'Leche Entera',
                'category_id' => $lacteos?->id,
                'unit_id' => $L?->id,
                'min_stock' => 50,
                'max_stock' => 200,
                'location_id' => $refrig?->id,
                'preferred_supplier_id' => $supplier?->id,
                'shelf_life_days' => 14,
                'unit_cost' => 18.00,
            ],
            [
                'code' => 'PROD-003',
                'name' => 'Aceite Vegetal',
                'category_id' => $aceites?->id,
                'unit_id' => $L?->id,
                'min_stock' => 30,
                'max_stock' => 150,
                'location_id' => $bodega?->id,
                'preferred_supplier_id' => $supplier?->id,
                'unit_cost' => 35.00,
            ],
            [
                'code' => 'PROD-004',
                'name' => 'Azucar Refinada',
                'category_id' => $azucar?->id,
                'unit_id' => $kg?->id,
                'min_stock' => 80,
                'max_stock' => 300,
                'location_id' => $bodega?->id,
                'preferred_supplier_id' => $supplier?->id,
                'shelf_life_days' => 730,
                'unit_cost' => 22.00,
            ],
            [
                'code' => 'PROD-005',
                'name' => 'Sal de Mesa',
                'category_id' => $condimentos?->id,
                'unit_id' => $kg?->id,
                'min_stock' => 20,
                'max_stock' => 100,
                'location_id' => $shelf?->id,
                'preferred_supplier_id' => $supplier?->id,
                'unit_cost' => 8.00,
            ],
            [
                'code' => 'PROD-006',
                'name' => 'Mantequilla',
                'category_id' => $lacteos?->id,
                'unit_id' => $kg?->id,
                'min_stock' => 10,
                'max_stock' => 50,
                'location_id' => $refrig?->id,
                'preferred_supplier_id' => $supplier?->id,
                'shelf_life_days' => 30,
                'unit_cost' => 85.00,
            ],
            [
                'code' => 'PROD-007',
                'name' => 'Harina de Maiz',
                'category_id' => $harina?->id,
                'unit_id' => $kg?->id,
                'min_stock' => 50,
                'max_stock' => 250,
                'location_id' => $bodega?->id,
                'shelf_life_days' => 180,
                'unit_cost' => 10.00,
            ],
            [
                'code' => 'PROD-008',
                'name' => 'Levadura Seca',
                'category_id' => $condimentos?->id,
                'unit_id' => $kg?->id,
                'min_stock' => 5,
                'max_stock' => 30,
                'location_id' => $shelf?->id,
                'shelf_life_days' => 365,
                'unit_cost' => 120.00,
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['code' => $productData['code']],
                array_merge($productData, ['is_active' => true, 'current_stock' => 0])
            );
        }
    }
}
