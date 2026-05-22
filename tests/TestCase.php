<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
<<<<<<< HEAD
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }
=======
    //
>>>>>>> parent of 90480f76 (chore: align dependencies and restore compatibility for PHP 8.2 and Filament v3)
}
