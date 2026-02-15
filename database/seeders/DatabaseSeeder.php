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
        // User::factory(10)->create();
        $offices = office::factory(10)->create();
        $this->call([
            RoleSeeder::class,
        ]);

       $user = User::factory()->create([
            'name'=>'admin',
            'last_name'=> 'Admin',
            'email'=> 'admin@admin.com',
            'number' => '00000000',
            'password'=> Hash::make('123'),
            'status'=>true,
            'office_id' => $offices->random()->id
        ]);

        $user->assignRole('Administrador');

    }
}
