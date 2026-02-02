<?php

// ABOUTME: Feature tests for the Admin CategoryController.
// ABOUTME: Covers CRUD operations, validation, and access control.

namespace Tests\Feature\Admin;

use App\Models\Category;
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
}
