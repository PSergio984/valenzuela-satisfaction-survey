<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

test('guests are redirected to the login page', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

test('authenticated admin users can visit the dashboard', function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user);

    $this->get('/admin')->assertOk();
});
