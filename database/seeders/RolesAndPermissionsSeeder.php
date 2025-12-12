<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Role management
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',

            // Permission management
            'view_permissions',
            'assign_permissions',

            // Survey management
            'view_surveys',
            'create_surveys',
            'edit_surveys',
            'delete_surveys',

            // Question management
            'view_questions',
            'create_questions',
            'edit_questions',
            'delete_questions',

            // Response management
            'view_responses',
            'delete_responses',
            'export_responses',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view_users',
            'view_surveys',
            'create_surveys',
            'edit_surveys',
            'delete_surveys',
            'view_questions',
            'create_questions',
            'edit_questions',
            'delete_questions',
            'view_responses',
            'delete_responses',
            'export_responses',
        ]);

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
