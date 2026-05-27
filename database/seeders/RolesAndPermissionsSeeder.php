<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

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

        // Generate Shield permissions dynamically
        \Illuminate\Support\Facades\Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
            '--option' => 'policies_and_permissions',
            '--no-interaction' => true,
            '--ignore-existing-policies' => true,
        ]);

        // Create roles and assign permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->syncPermissions(Permission::all());

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([
            // Basic permissions (expected by tests)
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

            // Shield permissions (expected by policies)
            'ViewAny:User',
            'View:User',
            'ViewAny:Survey',
            'View:Survey',
            'Create:Survey',
            'Update:Survey',
            'Delete:Survey',
            'ViewAny:Question',
            'View:Question',
            'Create:Question',
            'Update:Question',
            'Delete:Question',
            'ViewAny:Response',
            'View:Response',
            'Delete:Response',
        ]);

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
