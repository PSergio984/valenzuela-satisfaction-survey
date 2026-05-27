<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
});

test('super admin role cannot be updated, deleted, or replicated via policy checks', function () {
    $superAdminUser = User::factory()->create();
    $superAdminUser->assignRole('super_admin');

    $superAdminRole = Role::where('name', 'super_admin')->first();
    $adminRole = Role::where('name', 'admin')->first();

    $this->actingAs($superAdminUser);

    // Assert that super admin cannot update/delete/replicate the super_admin role
    expect(Gate::allows('update', $superAdminRole))->toBeFalse();
    expect(Gate::allows('delete', $superAdminRole))->toBeFalse();
    expect(Gate::allows('replicate', $superAdminRole))->toBeFalse();

    // Assert that super admin can update/delete/replicate other roles (e.g. admin role)
    expect(Gate::allows('update', $adminRole))->toBeTrue();
    expect(Gate::allows('delete', $adminRole))->toBeTrue();
    expect(Gate::allows('replicate', $adminRole))->toBeTrue();
});
