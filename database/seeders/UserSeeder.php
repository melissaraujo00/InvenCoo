<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $adminRole = Role::firstOrCreate(['name' => 'Administrador','guard_name' => 'web'
]);

        // Lista de permisos
        $permissions = [
            //usuarios
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            //inventario
            'ver inventario',
            'ver categorias',
            'ver marcas',
            'ver productos',
            'ver proveedores',
            //categoria
            'crear categoria',
            'editar categoria',
            'eliminar categoria',
            //marcas
            'crear marca',
            'editar marca',
            'eliminar marca',
            //productos
            'crear producto',
            'editar producto',
            'eliminar producto',
            //roles
            'ver Roles y Permisos',
            'Ver Roles',
            'Crear Rol',
            'Editar Rol',
            'Elimnar Rol',
            'Ver Permisos',
            'Crear Permiso',
            'Editar Permiso',
            //movimiento
            'Ver Movimientos y Tipos',
            'ver movimientos',
            'crear movimientos',
            'editar movimientos',
            'eliminar movimientos',

            //proveedores
            'crear proveedor',
            'editar proveedor',
            'eliminar proveedor',

            //compras
            'ver compras',
            'crear compra',
            'editar compra',
            'anular compra',
            //tranferncias
            'ver transferencias',
            'crear transferencia',
            'editar transferencia',
            //solicitar
            'solicitar compra',
            'crear solicitud compra',
            //reportes
            'ver reportes',
            'Aprobar'

        ];

        // Crear los permisos si no existen
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar todos los permisos al rol Administrador
        $adminRole->syncPermissions(Permission::all());

        $user = User::find(1);
        if ($user) {
            $user->assignRole($adminRole);
        }
    }
}
