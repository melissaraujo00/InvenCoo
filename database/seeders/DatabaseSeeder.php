<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\office;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            OfficeSeeder::class,
            TypeSeeder::class
        ]);


        $adminUser =User::factory()->create([
            'name'=>'admin',
            'last_name'=> 'Admin',
            'email'=> 'admin@admin.com',
            'number' => '00000000',
            'password'=> Hash::make('123'),
            'status'=>true,
            'office_id' => 1
        ]);

        $adminUser->assignRole('Administrador');


    }
}
