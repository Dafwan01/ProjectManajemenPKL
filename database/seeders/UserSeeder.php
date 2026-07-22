<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'password' => Hash::make('akuadmin'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'mentor',
            'email' => 'mentor@example.com',
            'password' => Hash::make('akumentor'),
            'role' => 'mentor',
        ]);

        User::create([
            'name' => 'pkl',
            'email' => 'pkl@example.com',
            'password' => Hash::make('akupkl'),
            'role' => 'pkl',
        ]);
    }
}
