<?php

namespace Tests\Unit;

use App\Services\CategoryResolver;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Unit tests for CategoryResolver service.
 *
 * Tests verify slug resolution and category mapping logic.
 *
 * To run these tests:
 *   php artisan test --filter=CategoryResolverTest
 */
class CategoryResolverTest extends TestCase
{
    protected CategoryResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new CategoryResolver();
        Cache::flush();
    }

    /**
     * Test that unknown slugs are treated as leaf categories.
     */
    public function test_unknown_slug_treated_as_leaf(): void
    {
        $result = $this->resolver->resolve('some-unknown-category');

        $this->assertEquals(['some-unknown-category'], $result['slugs']);
        $this->assertEquals('leaf', $result['active']['type']);
    }

    /**
     * Test that hashSlugs returns 'all' for empty array.
     */
    public function test_hash_slugs_returns_all_for_empty(): void
    {
        $this->assertEquals('all', $this->resolver->hashSlugs([]));
    }

    /**
     * Test that hashSlugs returns consistent hash for same slugs.
     */
    public function test_hash_slugs_is_consistent(): void
    {
        $slugs = ['cat-a', 'cat-b', 'cat-c'];

        $hash1 = $this->resolver->hashSlugs($slugs);
        $hash2 = $this->resolver->hashSlugs($slugs);

        $this->assertEquals($hash1, $hash2);
        $this->assertEquals(32, strlen($hash1)); // MD5 hash length
    }

    /**
     * Test that hashSlugs sorts slugs for consistent ordering.
     */
    public function test_hash_slugs_sorts_for_consistency(): void
    {
        $hash1 = $this->resolver->hashSlugs(['b', 'a', 'c']);
        $hash2 = $this->resolver->hashSlugs(['c', 'a', 'b']);

        $this->assertEquals($hash1, $hash2);
    }

    /**
     * Test that getCatalog returns array.
     */
    public function test_get_catalog_returns_array(): void
    {
        $catalog = $this->resolver->getCatalog();
        $this->assertIsArray($catalog);
    }

    /**
     * Test that getCategoryMap returns array.
     */
    public function test_get_category_map_returns_array(): void
    {
        $map = $this->resolver->getCategoryMap();
        $this->assertIsArray($map);
    }

    /**
     * Test that resolve returns expected structure.
     */
    public function test_resolve_returns_expected_structure(): void
    {
        $result = $this->resolver->resolve('test-slug');

        $this->assertArrayHasKey('slugs', $result);
        $this->assertArrayHasKey('active', $result);
        $this->assertIsArray($result['slugs']);
        $this->assertIsArray($result['active']);

        // Check active has required keys
        $this->assertArrayHasKey('slug', $result['active']);
        $this->assertArrayHasKey('gender', $result['active']);
        $this->assertArrayHasKey('section', $result['active']);
        $this->assertArrayHasKey('name', $result['active']);
        $this->assertArrayHasKey('type', $result['active']);
    }

    /**
     * Test that slug is normalized to lowercase.
     */
    public function test_slug_is_normalized(): void
    {
        $result = $this->resolver->resolve('TEST-SLUG');

        $this->assertEquals('test-slug', $result['active']['slug']);
    }
}
