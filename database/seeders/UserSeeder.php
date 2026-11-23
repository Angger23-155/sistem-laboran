<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan ini ditambahkan
use Illuminate\Support\Facades\Hash; // Pastikan ini ditambahkan

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Akun SuperAdmin
        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@lab.com',
            'password' => Hash::make('password123'), // Password default
            'role' => 'superadmin',
            'no_wa' => '628111111111', // Format 62 untuk WAHA
        ]);

        // Akun Kepala Lab
        User::create([
            'name' => 'Kepala Laboratorium',
            'email' => 'kepalalab@lab.com',
            'password' => Hash::make('password123'),
            'role' => 'kepala_lab',
            'no_wa' => '628222222222',
        ]);

        // Akun Admin
        User::create([
            'name' => 'Admin Lab',
            'email' => 'admin@lab.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'no_wa' => '628333333333',
        ]);

        // Akun Laboran
        User::create([
            'name' => 'Angger (Laboran)',
            'email' => 'angger@lab.com',
            'password' => Hash::make('password123'),
            'role' => 'laboran',
            'no_wa' => '628444444444',
        ]);
    }
}