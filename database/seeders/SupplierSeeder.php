<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'code' => 'SUP-001',
                'name' => 'Agricola Andina S.A.',
                'contact' => 'Juan Perez',
                'email' => 'contacto@agricolaandina.com',
                'phone' => '+57 301 234 5678',
                'tax_id' => '860.123.456-7',
                'is_active' => true,
            ],
            [
                'code' => 'SUP-002',
                'name' => 'Productos del Valle Ltda.',
                'contact' => 'Maria Garcia',
                'email' => 'ventas@productosdelvalles.com',
                'phone' => '+57 301 987 6543',
                'tax_id' => '860.234.567-8',
                'is_active' => true,
            ],
            [
                'code' => 'SUP-003',
                'name' => 'Alimentos Frescos Colombia',
                'contact' => 'Carlos Rodriguez',
                'email' => 'pedidos@alimentosfrescos.co',
                'phone' => '+57 301 555 2020',
                'tax_id' => '860.345.678-9',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(
                ['code' => $supplier['code']],
                $supplier
            );
        }
    }
}
