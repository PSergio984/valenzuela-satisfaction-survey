<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

test('guests are redirected to the login page', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

test('authenticated users can visit the dashboard', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->actingAs($user = User::factory()->create([
        'email_verified_at' => now(),
    ]));

    $this->get('/admin')->assertOk();
});
