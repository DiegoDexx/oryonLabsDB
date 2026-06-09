<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
 public function run(): void
{
    $user = \App\Models\User::updateOrCreate(
        ['email' => 'diegojspro@gmail.com'],
        [
            'name' => 'Admin User',
            'password' => Hash::make('oryonpanel325001'),
        ]
    );

    if (!$user->hasRole('admin')) {
        $user->assignRole('admin');
    }
}
}









