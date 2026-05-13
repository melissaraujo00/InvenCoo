<?php
namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;


class UnitOfMeasureSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            // === Peso ===
            ['name' => 'Gramos', 'symbol' => 'g', 'sort_order' => 1],
            ['name' => 'Kilogramos', 'symbol' => 'kg', 'sort_order' => 2],
            ['name' => 'Libras', 'symbol' => 'lb', 'sort_order' => 3],
            ['name' => 'Onzas', 'symbol' => 'oz', 'sort_order' => 4],

            // === Volumen (líquidos) ===
            ['name' => 'Mililitros', 'symbol' => 'ml', 'sort_order' => 5],
            ['name' => 'Litros', 'symbol' => 'L', 'sort_order' => 6],
            ['name' => 'Galones', 'symbol' => 'gal', 'sort_order' => 7],


            ['name' => 'Docenas', 'symbol' => 'doc', 'sort_order' => 8],
            ['name' => 'Piezas', 'symbol' => 'pz', 'sort_order' => 9],
            ['name' => 'Paquetes', 'symbol' => 'pqt', 'sort_order' => 10],
            ['name' => 'Bolsas', 'symbol' => 'bolsa', 'sort_order' => 11],
            ['name' => 'Cajas', 'symbol' => 'caja', 'sort_order' => 12],
            ['name' => 'Bandejas', 'symbol' => 'band', 'sort_order' => 13],
            ['name' => 'Frascos', 'symbol' => 'frasco', 'sort_order' => 14],
            ['name' => 'Botellas', 'symbol' => 'bot', 'sort_order' => 15],
            ['name' => 'Latas', 'symbol' => 'lata', 'sort_order' => 16],

        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
