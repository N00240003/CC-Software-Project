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
        User::insert([
            [
                'name' => 'John Doe',
                'email' => 'johndoe@email.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
            [
                'name' => 'Jane Dore',
                'email' => 'janedore@email.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
            [
                'name' => 'Cat Stevens',
                'email' => 'catstevens' . '@email.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
            [
                'name' => 'Audrey H. Burns',
                'email' => 'ahburns' . '@email.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
            [
                'name' => 'Mary Manx',
                'email' => 'marymanx' . '@email.com',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
        ]);
    }
}
