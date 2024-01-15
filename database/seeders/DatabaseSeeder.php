<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         \App\Models\User::factory(5)->create();

        \App\Models\User::factory()->create([
            'first_name' => 'Nur',
             'last_name' => 'Uddin',
             'email' => 'nur@tikweb.com',
             'designation_id' => 1,
             'password' => bcrypt('12345678'),
        ]);


        \App\Models\Designation::create([
            'name' => 'Software Engineer'
        ]);
    }
}
