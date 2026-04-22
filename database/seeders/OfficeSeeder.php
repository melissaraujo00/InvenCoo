<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Database/Seeders/OfficeSeeder.php
        Office::create(['id' => 1, 'name' => 'Cooperativa']);
        Office::create(['id' => 2, 'name' => 'Restaurante La Finca']);
    }
}
