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
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Pwd@12345'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super_admin');

        // Admin users
        $admin1 = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Pwd@12345'),
                'email_verified_at' => now(),
            ]
        );
        $admin1->assignRole('admin');

        $admin2 = User::firstOrCreate(
            ['email' => 'survey.admin@example.com'],
            [
                'name' => 'Survey Administrator',
                'password' => Hash::make('Pwd@12345'),
                'email_verified_at' => now(),
            ]
        );
        $admin2->assignRole('admin');

        // Additional admin users (formerly staff)
        $admin3 = User::firstOrCreate(
            ['email' => 'admin1@example.com'],
            [
                'name' => 'Maria Santos',
                'password' => Hash::make('Pwd@12345'),
                'email_verified_at' => now(),
            ]
        );
        $admin3->assignRole('admin');

        $admin4 = User::firstOrCreate(
            ['email' => 'admin2@example.com'],
            [
                'name' => 'Juan Dela Cruz',
                'password' => Hash::make('Pwd@12345'),
                'email_verified_at' => now(),
            ]
        );
        $admin4->assignRole('admin');

        $admin5 = User::firstOrCreate(
            ['email' => 'admin3@example.com'],
            [
                'name' => 'Ana Reyes',
                'password' => Hash::make('Pwd@12345'),
                'email_verified_at' => now(),
            ]
        );
        $admin5->assignRole('admin');

        // Demo/Test users without roles (regular users)
        User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('Pwd@12345'),
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('Pwd@12345'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Users seeded successfully!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin', 'superadmin@gmail.com', 'Pwd@12345'],
                ['Admin', 'admin@example.com', 'Pwd@12345'],
                ['Admin', 'survey.admin@example.com', 'Pwd@12345'],
                ['Admin', 'admin1@example.com', 'Pwd@12345'],
                ['Admin', 'admin2@example.com', 'Pwd@12345'],
                ['Admin', 'admin3@example.com', 'Pwd@12345'],
                ['(none)', 'demo@example.com', 'Pwd@12345'],
                ['(none)', 'test@example.com', 'Pwd@12345'],
            ]
        );
    }
}
