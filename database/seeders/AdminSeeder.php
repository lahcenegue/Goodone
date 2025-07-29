<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Check if any admin already exists
        if (Admin::count() > 0) {
            $this->command->info('Admin users already exist in the database.');
            return;
        }

        // Create the first admin user
        $admin = Admin::create([
            'name' => 'Hany admin',
            'email' => 'hanon83@gmail.com',
            'password' => Hash::make('Admin@2025!'),
            'active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: hanon83@gmail.com');
        $this->command->info('Password: Admin@2025!');
        $this->command->warn('IMPORTANT: Please change this password after first login!');
    }
}