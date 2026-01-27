<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'ADMIN',
        ]);

        // Create regular user
        User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'password' => Hash::make('password'),
            'role' => 'USER',
        ]);
    }
}
