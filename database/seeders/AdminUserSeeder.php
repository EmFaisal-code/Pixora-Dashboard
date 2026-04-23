<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin EM Indonesia',
            'email' => 'admin@emindonesia.org',
            'password' => Hash::make('EMI@Admin2026!'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@emindonesia.org',
            'password' => Hash::make('SuperEMI@2026!'),
            'email_verified_at' => now(),
        ]);
        
        // Log the creation of admin users
        \Log::info('Admin users created during seeding with 2026 passwords');
    }
}