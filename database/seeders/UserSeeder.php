<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@simp.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('Admin123!'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Warehouse Manager
        User::firstOrCreate(
            ['email' => 'gerente@simp.com'],
            [
                'name' => 'Gerente de Almacen',
                'password' => bcrypt('Pass1234!'),
                'role' => 'warehouse_manager',
                'is_active' => true,
            ]
        );

        // Warehouse Clerk
        User::firstOrCreate(
            ['email' => 'empleado@simp.com'],
            [
                'name' => 'Empleado de Almacen',
                'password' => bcrypt('Pass1234!'),
                'role' => 'warehouse_clerk',
                'is_active' => true,
            ]
        );

        // Production
        User::firstOrCreate(
            ['email' => 'produccion@simp.com'],
            [
                'name' => 'Departamento Produccion',
                'password' => bcrypt('Pass1234!'),
                'role' => 'production',
                'is_active' => true,
            ]
        );

        // Quality
        User::firstOrCreate(
            ['email' => 'calidad@simp.com'],
            [
                'name' => 'Control de Calidad',
                'password' => bcrypt('Pass1234!'),
                'role' => 'quality',
                'is_active' => true,
            ]
        );

        // Purchasing
        User::firstOrCreate(
            ['email' => 'compras@simp.com'],
            [
                'name' => 'Departamento Compras',
                'password' => bcrypt('Pass1234!'),
                'role' => 'purchasing',
                'is_active' => true,
            ]
        );
    }
}
