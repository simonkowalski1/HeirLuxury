<?php

// ABOUTME: Feature tests for the ContactController (inquiry form submission).
// ABOUTME: Covers validation, email sending, and error handling.

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_inquiry_submission_returns_success_json(): void
    {
        $response = $this->postJson('/inquiry', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'message' => 'I am interested in this product.',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_inquiry_requires_first_name(): void
    {
        $response = $this->postJson('/inquiry', [
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'message' => 'Hello',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('first_name');
    }

    public function test_inquiry_requires_last_name(): void
    {
        $response = $this->postJson('/inquiry', [
            'first_name' => 'John',
            'email' => 'john@example.com',
            'message' => 'Hello',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('last_name');
    }

    public function test_inquiry_requires_email(): void
    {
        $response = $this->postJson('/inquiry', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'message' => 'Hello',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
    }

    public function test_inquiry_requires_valid_email(): void
    {
        $response = $this->postJson('/inquiry', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'not-an-email',
            'message' => 'Hello',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
    }

    public function test_inquiry_requires_message(): void
    {
        $response = $this->postJson('/inquiry', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('message');
    }

    public function test_inquiry_accepts_optional_phone(): void
    {
        $response = $this->postJson('/inquiry', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'message' => 'Hello',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }

    public function test_inquiry_accepts_optional_product_fields(): void
    {
        $response = $this->postJson('/inquiry', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'message' => 'Tell me more about this',
            'product_name' => 'Neverfull MM',
            'product_slug' => 'neverfull-mm',
            'product_url' => '/en/catalog/lv-women-bags/neverfull-mm',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }

    public function test_inquiry_sends_email(): void
    {
        $this->postJson('/inquiry', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'message' => 'Interested in buying.',
        ]);

        Mail::assertSent(\App\Mail\ContactMail::class);
    }
}
