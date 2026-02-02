<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock Vite to avoid manifest not found errors in tests
        $this->withoutVite();

        // Disable CSRF verification for tests
        $this->withoutMiddleware(VerifyCsrfToken::class);

        Storage::fake('public');

        $this->admin = User::factory()->create();
        $this->admin->is_admin = true;
        $this->admin->save();

        $this->user = User::factory()->create();
    }

    // ==================== Index Tests ====================

    public function test_products_index_is_accessible_by_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products.index'));

        $response->assertOk();
        $response->assertViewIs('admin.products.index');
    }

    public function test_products_index_is_not_accessible_by_regular_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.products.index'));

        $response->assertForbidden();
    }

    public function test_products_index_displays_products(): void
    {
        $products = Product::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.products.index'));

        $response->assertOk();
        foreach ($products as $product) {
            $response->assertSee($product->name);
        }
    }

    public function test_products_index_paginates_results(): void
    {
        Product::factory()->count(25)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.products.index'));

        $response->assertOk();
        $response->assertViewHas('products', function ($products) {
            return $products->count() === 20;
        });
    }

    // ==================== Search & Filter Tests ====================

    public function test_products_can_be_searched_by_name(): void
    {
        Product::factory()->create(['name' => 'Neverfull Tote']);
        Product::factory()->create(['name' => 'Speedy Bag']);
        Product::factory()->create(['name' => 'Alma PM']);

        $response = $this->actingAs($this->admin)->get(route('admin.products.index', ['search' => 'Neverfull']));

        $response->assertOk();
        $response->assertSee('Neverfull Tote');
        $response->assertDontSee('Speedy Bag');
        $response->assertDontSee('Alma PM');
    }

    public function test_products_can_be_filtered_by_brand(): void
    {
        Product::factory()->forBrand('Louis Vuitton')->create(['name' => 'LV Product']);
        Product::factory()->forBrand('Chanel')->create(['name' => 'Chanel Product']);

        $response = $this->actingAs($this->admin)->get(route('admin.products.index', ['brand' => 'Louis Vuitton']));

        $response->assertOk();
        $response->assertSee('LV Product');
        $response->assertDontSee('Chanel Product');
    }

    public function test_products_can_be_filtered_by_gender(): void
    {
        Product::factory()->forGender('women')->create(['name' => 'Women Product']);
        Product::factory()->forGender('men')->create(['name' => 'Men Product']);

        $response = $this->actingAs($this->admin)->get(route('admin.products.index', ['gender' => 'women']));

        $response->assertOk();
        $response->assertSee('Women Product');
        $response->assertDontSee('Men Product');
    }

    public function test_search_and_filter_can_be_combined(): void
    {
        Product::factory()->forBrand('Louis Vuitton')->forGender('women')->create(['name' => 'LV Women Bag']);
        Product::factory()->forBrand('Louis Vuitton')->forGender('men')->create(['name' => 'LV Men Wallet']);
        Product::factory()->forBrand('Chanel')->forGender('women')->create(['name' => 'Chanel Women Bag']);

        $response = $this->actingAs($this->admin)->get(route('admin.products.index', [
            'search' => 'Bag',
            'brand' => 'Louis Vuitton',
        ]));

        $response->assertOk();
        $response->assertSee('LV Women Bag');
        $response->assertDontSee('LV Men Wallet');
        $response->assertDontSee('Chanel Women Bag');
    }

    public function test_filter_options_are_available(): void
    {
        Product::factory()->forBrand('Louis Vuitton')->create();
        Product::factory()->forBrand('Chanel')->create();

        $response = $this->actingAs($this->admin)->get(route('admin.products.index'));

        $response->assertOk();
        $response->assertViewHas('brands', function ($brands) {
            return $brands->contains('Louis Vuitton') && $brands->contains('Chanel');
        });
        $response->assertViewHas('genders');
    }

    // ==================== CRUD Tests ====================

    public function test_create_product_form_is_accessible(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.products.create'));

        $response->assertOk();
        $response->assertViewIs('admin.products.create');
        $response->assertViewHas('categories');
    }

    public function test_product_can_be_created(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'name' => 'New Test Product',
                'brand' => 'Louis Vuitton',
                'gender' => 'women',
                'section' => 'bags',
                'category_slug' => $category->slug,
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'New Test Product',
            'brand' => 'Louis Vuitton',
        ]);
    }

    public function test_product_can_be_created_with_image(): void
    {
        $category = Category::factory()->create();
        $file = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'name' => 'Product With Image',
                'brand' => 'Chanel',
                'gender' => 'women',
                'section' => 'bags',
                'category_slug' => $category->slug,
                'image' => $file,
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Product With Image']);

        $product = Product::where('name', 'Product With Image')->first();
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_product_creation_requires_name(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'brand' => 'Louis Vuitton',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_edit_product_form_is_accessible(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('admin.products.edit', $product));

        $response->assertOk();
        $response->assertViewIs('admin.products.edit');
        $response->assertViewHas('product', $product);
    }

    public function test_product_can_be_updated(): void
    {
        $product = Product::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), [
                'name' => 'Updated Name',
                'brand' => $product->brand,
                'gender' => $product->gender,
                'section' => $product->section,
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_product_can_be_deleted(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    // ==================== Bulk Delete Tests ====================

    public function test_products_can_be_bulk_deleted(): void
    {
        $products = Product::factory()->count(5)->create();
        $ids = $products->pluck('id')->toArray();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.products.bulk-destroy'), [
                'ids' => $ids,
            ]);

        $response->assertRedirect(route('admin.products.index'));
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('products', ['id' => $id]);
        }
    }

    public function test_bulk_delete_with_empty_ids_shows_error(): void
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.products.bulk-destroy'), [
                'ids' => [],
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('error');
    }

    public function test_bulk_delete_deletes_product_images(): void
    {
        // Create a product with an image using factory and store
        $product = Product::factory()->create();

        // Manually store a file and update the product's image field
        $file = UploadedFile::fake()->image('product.jpg');
        $path = $file->store('products', 'public');
        $product->update(['image' => $path]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.products.bulk-destroy'), [
                'ids' => [$product->id],
            ]);

        $response->assertRedirect(route('admin.products.index'));
        Storage::disk('public')->assertMissing($path);
    }

    // ==================== Empty State Tests ====================

    public function test_products_index_shows_empty_state_when_no_products(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products.index'));

        $response->assertOk();
        $response->assertSee('No products found');
    }

    public function test_products_index_shows_empty_state_when_search_has_no_results(): void
    {
        Product::factory()->create(['name' => 'Neverfull']);

        $response = $this->actingAs($this->admin)->get(route('admin.products.index', ['search' => 'NonexistentProduct']));

        $response->assertOk();
        $response->assertSee('No products match your search');
    }
}
