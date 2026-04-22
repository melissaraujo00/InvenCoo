<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Compras', 'description' => 'Registro de entrada de productos adquiridos que ingresan al inventario de la sucursal.'],

            ['name' => 'Salida', 'description' => 'Registro de salida de productos desde la sucursal, lo cual reduce la cantidad disponible en el inventario.'],

            ['name' => 'Devoluciones', 'description' => 'Registro de ingreso al inventario de productos que han sido devueltos.'],

            ['name' => 'Ajuste', 'description' => 'Modificación realizada en el inventario para corregir errores o inconsistencias en los registros.'],
            ['name' => 'Transferencia Salida', 'description' => 'Salida de inventario por transferencia entre sucursales'],
    ['name' => 'Transferencia Entrada', 'description' => 'Entrada de inventario por transferencia entre sucursales'],


        ];

        foreach ($types as $type) {
            Type::create($type);
        }
    }
}
