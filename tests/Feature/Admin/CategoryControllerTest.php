<?php

// ABOUTME: Feature tests for the Admin CategoryController.
// ABOUTME: Covers CRUD operations, validation, and access control.

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->admin = User::factory()->create();
        $this->admin->is_admin = true;
        $this->admin->save();

        $this->user = User::factory()->create();
    }

    // ==================== Access Control ====================

    public function test_categories_index_is_accessible_by_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));

        $response->assertOk();
    }

    public function test_categories_index_is_not_accessible_by_regular_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.categories.index'));

        $response->assertForbidden();
    }

    public function test_categories_index_is_not_accessible_by_guest(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertRedirect(route('login'));
    }

    // ==================== Index ====================

    public function test_categories_index_displays_categories(): void
    {
        $category = Category::factory()->create(['name' => 'Women Bags']);

        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertSee('Women Bags');
    }

    public function test_categories_index_paginates_at_20_per_page(): void
    {
        Category::factory()->count(25)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertViewHas('categories', function ($categories) {
            return $categories->count() === 20;
        });
    }

    // ==================== Create ====================

    public function test_create_category_form_is_accessible(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.categories.create'));

        $response->assertOk();
    }

    public function test_category_can_be_created(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'New Category',
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'slug' => 'new-category',
        ]);
    }

    public function test_category_creation_auto_generates_slug(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Louis Vuitton Women Bags',
            ]);

        $this->assertDatabaseHas('categories', [
            'slug' => 'louis-vuitton-women-bags',
        ]);
    }

    public function test_category_creation_requires_name(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), []);

        $response->assertSessionHasErrors('name');
    }

    public function test_category_slug_must_be_unique(): void
    {
        Category::factory()->create(['slug' => 'women-bags']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Women Bags',
                'slug' => 'women-bags',
            ]);

        $response->assertSessionHasErrors('slug');
    }

    // ==================== Edit/Update ====================

    public function test_edit_category_form_is_accessible(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.edit', $category));

        $response->assertOk();
    }

    public function test_category_can_be_updated(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Updated Name',
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_category_update_allows_same_slug(): void
    {
        $category = Category::factory()->create(['slug' => 'women-bags']);

        // Updating with the same slug should not cause a uniqueness error
        $response = $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Women Bags Updated',
                'slug' => 'women-bags',
            ]);

        $response->assertRedirect(route('admin.categories.index'));
    }

    // ==================== Delete ====================

    public function test_category_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    // ==================== Product Count Tests ====================

    public function test_categories_index_shows_product_count(): void
    {
        $category = Category::factory()->create(['slug' => 'lv-women-bags']);
        Product::factory()->count(5)->create(['category_slug' => 'lv-women-bags']);

        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertSeeText('5');
    }

    public function test_categories_index_shows_zero_for_empty_categories(): void
    {
        Category::factory()->create(['slug' => 'empty-category']);

        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertSeeText('0');
    }

    public function test_categories_index_has_products_count_header(): void
    {
        Category::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertSeeText('Products');
    }

    // ==================== Search ====================

    public function test_search_filters_categories_by_name(): void
    {
        Category::factory()->create(['name' => 'Women Bags']);
        Category::factory()->create(['name' => 'Men Shoes']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['search' => 'Women']));

        $response->assertOk();
        $response->assertSee('Women Bags');
        $response->assertDontSee('Men Shoes');
    }

    public function test_search_filters_categories_by_slug(): void
    {
        Category::factory()->create(['name' => 'Women Bags', 'slug' => 'women-bags']);
        Category::factory()->create(['name' => 'Men Shoes', 'slug' => 'men-shoes']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['search' => 'men-shoes']));

        $response->assertOk();
        $response->assertSee('Men Shoes');
        $response->assertDontSee('Women Bags');
    }

    public function test_search_is_case_insensitive(): void
    {
        Category::factory()->create(['name' => 'Luxury Watches']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['search' => 'luxury']));

        $response->assertOk();
        $response->assertSee('Luxury Watches');
    }

    public function test_empty_search_returns_all_categories(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['search' => '']));

        $response->assertOk();
        $response->assertViewHas('categories', function ($categories) {
            return $categories->count() === 3;
        });
    }

    // ==================== Filtering ====================

    public function test_filter_has_products_shows_only_categories_with_products(): void
    {
        $withProducts = Category::factory()->create(['slug' => 'has-products']);
        Product::factory()->create(['category_slug' => 'has-products']);

        $empty = Category::factory()->create(['slug' => 'no-products']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['has_products' => '1']));

        $response->assertOk();
        $response->assertSee($withProducts->name);
        $response->assertDontSee($empty->name);
    }

    public function test_filter_empty_shows_only_categories_without_products(): void
    {
        $withProducts = Category::factory()->create(['slug' => 'has-products']);
        Product::factory()->create(['category_slug' => 'has-products']);

        $empty = Category::factory()->create(['slug' => 'no-products']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['has_products' => '0']));

        $response->assertOk();
        $response->assertSee($empty->name);
        $response->assertDontSee($withProducts->name);
    }

    // ==================== Sorting ====================

    public function test_sort_by_name_ascending(): void
    {
        Category::factory()->create(['name' => 'Zebra']);
        Category::factory()->create(['name' => 'Alpha']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['sort' => 'name', 'direction' => 'asc']));

        $response->assertOk();
        $response->assertSeeInOrder(['Alpha', 'Zebra']);
    }

    public function test_sort_by_name_descending(): void
    {
        Category::factory()->create(['name' => 'Alpha']);
        Category::factory()->create(['name' => 'Zebra']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['sort' => 'name', 'direction' => 'desc']));

        $response->assertOk();
        $response->assertSeeInOrder(['Zebra', 'Alpha']);
    }

    public function test_sort_by_products_count(): void
    {
        $many = Category::factory()->create(['name' => 'Popular', 'slug' => 'popular']);
        Product::factory()->count(10)->create(['category_slug' => 'popular']);

        $few = Category::factory()->create(['name' => 'Niche', 'slug' => 'niche']);
        Product::factory()->count(1)->create(['category_slug' => 'niche']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['sort' => 'products_count', 'direction' => 'desc']));

        $response->assertOk();
        $response->assertSeeInOrder(['Popular', 'Niche']);
    }

    public function test_default_sort_is_name_ascending(): void
    {
        Category::factory()->create(['name' => 'Zebra']);
        Category::factory()->create(['name' => 'Alpha']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Alpha', 'Zebra']);
    }

    public function test_invalid_sort_column_falls_back_to_name(): void
    {
        Category::factory()->create(['name' => 'Zebra']);
        Category::factory()->create(['name' => 'Alpha']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['sort' => 'malicious_column']));

        $response->assertOk();
        $response->assertSeeInOrder(['Alpha', 'Zebra']);
    }

    public function test_search_preserves_sort_params_in_pagination(): void
    {
        Category::factory()->count(25)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['search' => 'test', 'sort' => 'name', 'direction' => 'desc']));

        $response->assertOk();
    }

    public function test_search_and_filter_can_combine(): void
    {
        $match = Category::factory()->create(['name' => 'Luxury Bags', 'slug' => 'luxury-bags']);
        Product::factory()->create(['category_slug' => 'luxury-bags']);

        $noProducts = Category::factory()->create(['name' => 'Luxury Shoes', 'slug' => 'luxury-shoes']);

        $wrongName = Category::factory()->create(['name' => 'Men Watches', 'slug' => 'men-watches']);
        Product::factory()->create(['category_slug' => 'men-watches']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['search' => 'Luxury', 'has_products' => '1']));

        $response->assertOk();
        $response->assertSee('Luxury Bags');
        $response->assertDontSee('Luxury Shoes');
        $response->assertDontSee('Men Watches');
    }
}
