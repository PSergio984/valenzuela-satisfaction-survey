<?php

use App\Models\Survey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('public survey list is paginated', function () {
    Survey::factory()->count(15)->create([
        'is_active' => true,
        'is_public' => true,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
    ]);

    $response = $this->get(route('surveys.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('surveys/index')
        ->has('surveys.data', 12)
        ->has('surveys.links')
    );
});

test('public survey list can be searched by title', function () {
    Survey::factory()->create([
        'title' => 'Unique Searchable Survey',
        'is_active' => true,
        'is_public' => true,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
    ]);
    Survey::factory()->count(5)->create([
        'is_active' => true,
        'is_public' => true,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
    ]);

    $response = $this->get(route('surveys.index', ['search' => 'Unique']));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('surveys/index')
        ->has('surveys.data', 1)
        ->where('surveys.data.0.title', 'Unique Searchable Survey')
    );
});
