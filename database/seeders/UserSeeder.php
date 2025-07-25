<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@warehouse.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Manager',
            'email' => 'manager@warehouse.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Staff',
            'email' => 'staff@warehouse.com',
            'password' => Hash::make('password'),
        ]);
    }
}
