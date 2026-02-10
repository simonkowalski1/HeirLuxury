<?php

// ABOUTME: Feature tests for category unification (config → database).
// ABOUTME: Covers seeder, navigation data generation, admin CRUD with new fields.

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryUnificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->admin = User::factory()->create();
        $this->admin->is_admin = true;
        $this->admin->save();
    }

    // ==================== Seeder ====================

    public function test_category_seeder_populates_database(): void
    {
        $this->seed(CategorySeeder::class);

        // Config has many categories; DB should have them all
        $this->assertGreaterThan(0, Category::count());
    }

    public function test_category_seeder_sets_gender(): void
    {
        $this->seed(CategorySeeder::class);

        $womenCount = Category::where('gender', 'women')->count();
        $menCount = Category::where('gender', 'men')->count();

        $this->assertGreaterThan(0, $womenCount);
        $this->assertGreaterThan(0, $menCount);
    }

    public function test_category_seeder_sets_section(): void
    {
        $this->seed(CategorySeeder::class);

        $bagsCount = Category::where('section', 'bags')->count();
        $shoesCount = Category::where('section', 'shoes')->count();

        $this->assertGreaterThan(0, $bagsCount);
        $this->assertGreaterThan(0, $shoesCount);
    }

    public function test_category_seeder_sets_brand(): void
    {
        $this->seed(CategorySeeder::class);

        $this->assertDatabaseHas('categories', [
            'slug' => 'louis-vuitton-women-bags',
            'brand' => 'Louis Vuitton',
        ]);
    }

    public function test_category_seeder_is_idempotent(): void
    {
        $this->seed(CategorySeeder::class);
        $firstCount = Category::count();

        $this->seed(CategorySeeder::class);
        $secondCount = Category::count();

        $this->assertEquals($firstCount, $secondCount);
    }

    // ==================== Navigation Data ====================

    public function test_get_navigation_data_returns_gender_sections(): void
    {
        Category::factory()->create([
            'name' => 'Test Bags',
            'slug' => 'test-women-bags',
            'gender' => 'women',
            'section' => 'bags',
            'brand' => 'Test',
            'is_active' => true,
        ]);

        $data = Category::getNavigationData();

        $this->assertArrayHasKey('women', $data);
        $this->assertArrayHasKey('men', $data);
        $this->assertArrayHasKey('Bags', $data['women']);
        $this->assertCount(1, $data['women']['Bags']);
        $this->assertEquals('Test Bags', $data['women']['Bags'][0]['name']);
    }

    public function test_get_navigation_data_excludes_inactive(): void
    {
        Category::factory()->create([
            'name' => 'Active Cat',
            'slug' => 'active-cat',
            'gender' => 'women',
            'section' => 'bags',
            'is_active' => true,
        ]);

        Category::factory()->create([
            'name' => 'Inactive Cat',
            'slug' => 'inactive-cat',
            'gender' => 'women',
            'section' => 'bags',
            'is_active' => false,
        ]);

        $data = Category::getNavigationData();

        $names = collect($data['women']['Bags'] ?? [])->pluck('name')->all();
        $this->assertContains('Active Cat', $names);
        $this->assertNotContains('Inactive Cat', $names);
    }

    public function test_get_navigation_data_skips_categories_without_gender(): void
    {
        Category::factory()->create([
            'name' => 'No Gender',
            'slug' => 'no-gender',
            'gender' => null,
            'section' => 'bags',
            'is_active' => true,
        ]);

        $data = Category::getNavigationData();

        $this->assertEmpty($data['women']);
        $this->assertEmpty($data['men']);
    }

    // ==================== Admin CRUD with New Fields ====================

    public function test_create_category_with_taxonomy_fields(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Gucci Women Bags',
                'gender' => 'women',
                'section' => 'bags',
                'brand' => 'Gucci',
                'display_order' => 5,
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Gucci Women Bags',
            'gender' => 'women',
            'section' => 'bags',
            'brand' => 'Gucci',
            'display_order' => 5,
            'is_active' => true,
        ]);
    }

    public function test_update_category_taxonomy_fields(): void
    {
        $category = Category::factory()->create([
            'gender' => 'women',
            'section' => 'bags',
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $category), [
                'name' => $category->name,
                'gender' => 'men',
                'section' => 'shoes',
                'brand' => 'Nike',
                'display_order' => 10,
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'gender' => 'men',
            'section' => 'shoes',
            'brand' => 'Nike',
            'display_order' => 10,
        ]);
    }

    public function test_category_is_active_defaults_to_false_when_unchecked(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Inactive Category',
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Inactive Category',
            'is_active' => false,
        ]);
    }

    public function test_edit_form_shows_taxonomy_fields(): void
    {
        $category = Category::factory()->create([
            'gender' => 'women',
            'section' => 'bags',
            'brand' => 'Gucci',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.edit', $category));

        $response->assertOk();
        $response->assertSee('Gender');
        $response->assertSee('Section');
        $response->assertSee('Brand');
    }

    public function test_create_form_shows_taxonomy_fields(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.create'));

        $response->assertOk();
        $response->assertSee('Gender');
        $response->assertSee('Section');
        $response->assertSee('Brand');
    }
}
