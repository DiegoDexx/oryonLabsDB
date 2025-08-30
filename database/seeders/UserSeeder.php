<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //crear seeder de 1 user
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'diegojspro@gmail.com',
            'password' => bcrypt('oryonpanel325001'),
        ]);

        \App\Models\User::find(1)->assignRole('admin');
    }
}
