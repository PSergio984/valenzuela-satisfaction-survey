<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CleanupStaffRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reassign all staff users to admin role
        $staffRole = Role::where('name', 'staff')->first();

        if ($staffRole) {
            $adminRole = Role::where('name', 'admin')->first();

            // Get all users with staff role
            $staffUsers = User::role('staff')->get();

            foreach ($staffUsers as $user) {
                // Remove staff role and assign admin role
                $user->removeRole('staff');
                $user->assignRole('admin');
            }

            // Delete the staff role
            $staffRole->delete();

            $this->command->info('Staff role removed and users reassigned to admin role!');
        } else {
            $this->command->info('Staff role does not exist.');
        }
    }
}
