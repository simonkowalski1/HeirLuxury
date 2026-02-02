<?php

// ABOUTME: Feature tests for the HomeController.
// ABOUTME: Covers home page rendering and product carousel data.

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_home_page_returns_200_with_english_locale(): void
    {
        $response = $this->get('/en');

        $response->assertOk();
    }

    public function test_home_page_returns_200_with_polish_locale(): void
    {
        $response = $this->get('/pl');

        $response->assertOk();
    }

    public function test_home_page_passes_new_additions_to_view(): void
    {
        Product::factory()->count(15)->create();

        $response = $this->get('/en');

        $response->assertOk();
        $response->assertViewHas('newAdditions', function ($additions) {
            return $additions->count() === 9;
        });
    }

    public function test_home_page_handles_empty_product_database(): void
    {
        $response = $this->get('/en');

        $response->assertOk();
        $response->assertViewHas('newAdditions', function ($additions) {
            return $additions->count() === 0;
        });
    }

    public function test_home_page_returns_fewer_products_when_less_than_9_exist(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->get('/en');

        $response->assertOk();
        $response->assertViewHas('newAdditions', function ($additions) {
            return $additions->count() === 3;
        });
    }

    public function test_root_url_redirects_to_english_home(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/en');
    }
}
