<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Administrador',  'guard_name' => 'web']);
        Role::create(['name' => 'Administrador Restaurante',  'guard_name' => 'web']);
        Role::create(['name' => 'Auditor',  'guard_name' => 'web']);
        Role::create(['name' => 'Bodega',  'guard_name' => 'web']);
    }
}
