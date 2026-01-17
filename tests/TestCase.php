<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test environment
        config(['app.env' => 'testing']);
        
        // Disable middleware that might interfere with tests
        // $this->withoutMiddleware([
        //     \App\Http\Middleware\VerifyCsrfToken::class,
        // ]);
    }
}
