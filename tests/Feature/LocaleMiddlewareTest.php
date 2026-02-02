<?php

// ABOUTME: Feature tests for the SetLocale middleware.
// ABOUTME: Verifies locale is correctly set from URL prefix for en and pl.

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_english_locale_sets_app_locale(): void
    {
        $this->get('/en');

        $this->assertEquals('en', app()->getLocale());
    }

    public function test_polish_locale_sets_app_locale(): void
    {
        $this->get('/pl');

        $this->assertEquals('pl', app()->getLocale());
    }

    public function test_invalid_locale_does_not_match_localized_routes(): void
    {
        // /de is not in the allowed locales (en|pl), so it won't match
        // the localized route group and should not return 200 from the home route
        $response = $this->get('/de');

        $response->assertNotFound();
    }
}
