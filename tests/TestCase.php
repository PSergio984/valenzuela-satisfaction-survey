<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

abstract class TestCase extends BaseTestCase
{
    // We don't use WithoutMiddleware globally to avoid breaking auth/route logic,
    // but we can selectively disable CSRF if needed. 
    // However, Laravel should handle this. Let's try adding a trait to Pest instead.
}
