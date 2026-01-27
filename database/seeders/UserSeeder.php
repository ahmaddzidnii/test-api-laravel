<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $faker = Faker::create('id_ID');

        $initialUsers = [
            [
                'name' => 'Atmin SCIT',
                'username' => 'admin',
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('admin'),
                'role' => 'SUPER_ADMIN',
            ],
        ];
        foreach ($initialUsers as $userData) {
            User::create($userData);
        }
    }
}
