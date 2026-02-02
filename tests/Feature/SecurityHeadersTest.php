<?php

// ABOUTME: Feature tests for the SecurityHeaders middleware.
// ABOUTME: Verifies security headers are present on all responses.

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_response_has_x_content_type_options_header(): void
    {
        $response = $this->get('/en');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_response_has_x_frame_options_header(): void
    {
        $response = $this->get('/en');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    public function test_response_has_x_xss_protection_header(): void
    {
        $response = $this->get('/en');

        $response->assertHeader('X-XSS-Protection', '1; mode=block');
    }

    public function test_response_has_referrer_policy_header(): void
    {
        $response = $this->get('/en');

        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_response_has_permissions_policy_header(): void
    {
        $response = $this->get('/en');

        $response->assertHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
    }
}
