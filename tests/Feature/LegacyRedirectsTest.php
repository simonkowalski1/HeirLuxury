<?php

// ABOUTME: Feature tests for legacy URL redirects.
// ABOUTME: Verifies old URLs redirect to localized equivalents with 301 status.

namespace Tests\Feature;

use Tests\TestCase;

class LegacyRedirectsTest extends TestCase
{
    public function test_legacy_catalog_redirects_to_localized(): void
    {
        $response = $this->get('/catalog');

        $response->assertRedirect('/en/catalog');
        $response->assertStatus(301);
    }

    public function test_legacy_catalog_category_redirects_to_localized(): void
    {
        $response = $this->get('/catalog/women-bags');

        $response->assertRedirect('/en/catalog/women-bags');
        $response->assertStatus(301);
    }

    public function test_legacy_categories_redirects_to_localized(): void
    {
        $response = $this->get('/categories');

        $response->assertRedirect('/en/catalog');
    }

    public function test_legacy_categories_slug_redirects_to_localized(): void
    {
        $response = $this->get('/categories/men-shoes');

        $response->assertRedirect('/en/catalog/men-shoes');
        $response->assertStatus(301);
    }
}
