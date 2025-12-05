<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed roles and permissions first
        $this->call(RolesAndPermissionsSeeder::class);

        // 2. Seed users with roles
        $this->call(UserSeeder::class);

        // 3. Seed surveys with questions and options
        $this->call(SurveySeeder::class);

        // 4. Seed responses with answers (generates comprehensive data for dashboard)
        $this->call(ResponseSeeder::class);
    }
}
