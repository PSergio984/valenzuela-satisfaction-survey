<?php

use App\Models\Survey;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public survey list is paginated', function () {
    Survey::factory()->count(15)->create(['is_active' => true]);

    $response = $this->get(route('surveys.index'));

    $response->assertOk();
    $response->assertHasPaginatedResource('surveys');
    
    // Assert 10 items per page (or whatever default)
    expect(count($response->viewData('surveys')->items()))->toBe(10);
})->todo();

test('public survey list can be searched by title', function () {
    Survey::factory()->create(['title' => 'Unique Searchable Survey', 'is_active' => true]);
    Survey::factory()->count(5)->create(['is_active' => true]);

    $response = $this->get(route('surveys.index', ['search' => 'Unique']));

    $response->assertOk();
    $response->assertSee('Unique Searchable Survey');
    expect(count($response->viewData('surveys')->items()))->toBe(1);
})->todo();
