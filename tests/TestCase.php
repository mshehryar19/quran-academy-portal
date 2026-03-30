<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Form submissions in feature tests do not send a CSRF token; Laravel 12 uses
        // ValidateCsrfToken in the `web` stack (replacement for legacy VerifyCsrfToken).
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }
}
