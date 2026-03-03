<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun Admin
        \App\Models\User::create([
            'name' => 'Admin Sitatik',
            'email' => 'admin@sitatik.com',
            'password' => Hash::make('password12'),
            'role' => 'admin',
        ]);

        // Akun Operator OPD
        \App\Models\User::create([
            'name' => 'Operator Diskominfo',
            'email' => 'opd@sitatik.com',
            'password' => Hash::make('password12'),
            'role' => 'operator',
        ]);
    }
}
