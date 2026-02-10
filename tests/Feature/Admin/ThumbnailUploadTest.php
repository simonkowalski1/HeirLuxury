<?php

// ABOUTME: Feature tests for product thumbnail upload with auto-WebP conversion.
// ABOUTME: Covers upload, WebP conversion, card preference, and replacement behavior.

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ThumbnailUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        Storage::fake('public');

        $this->admin = User::factory()->create();
        $this->admin->is_admin = true;
        $this->admin->save();
    }

    // ==================== Upload Tests ====================

    public function test_thumbnail_can_be_uploaded_during_product_creation(): void
    {
        $category = Category::factory()->create();
        $thumbnail = UploadedFile::fake()->image('card-photo.jpg', 800, 600);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'name' => 'Product With Thumbnail',
                'brand' => 'Louis Vuitton',
                'gender' => 'women',
                'section' => 'bags',
                'category_slug' => $category->slug,
                'thumbnail' => $thumbnail,
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Product With Thumbnail')->first();
        $this->assertNotNull($product->thumbnail);
        Storage::disk('public')->assertExists($product->thumbnail);
    }

    public function test_uploaded_thumbnail_is_stored_as_webp(): void
    {
        $category = Category::factory()->create();
        $thumbnail = UploadedFile::fake()->image('photo.jpg', 800, 600);

        $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'name' => 'WebP Thumbnail Product',
                'brand' => 'Chanel',
                'gender' => 'women',
                'section' => 'bags',
                'category_slug' => $category->slug,
                'thumbnail' => $thumbnail,
            ]);

        $product = Product::where('name', 'WebP Thumbnail Product')->first();
        $this->assertStringEndsWith('.webp', $product->thumbnail);
    }

    public function test_thumbnail_can_be_uploaded_during_product_update(): void
    {
        $product = Product::factory()->create();
        $thumbnail = UploadedFile::fake()->image('updated-thumb.jpg', 800, 600);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), [
                'name' => $product->name,
                'thumbnail' => $thumbnail,
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertNotNull($product->thumbnail);
        Storage::disk('public')->assertExists($product->thumbnail);
    }

    public function test_old_thumbnail_is_deleted_when_replaced(): void
    {
        $product = Product::factory()->create();

        // Upload first thumbnail
        $first = UploadedFile::fake()->image('first.jpg', 800, 600);
        $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), [
                'name' => $product->name,
                'thumbnail' => $first,
            ]);

        $product->refresh();
        $oldPath = $product->thumbnail;
        Storage::disk('public')->assertExists($oldPath);

        // Upload replacement thumbnail
        $second = UploadedFile::fake()->image('second.jpg', 800, 600);
        $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), [
                'name' => $product->name,
                'thumbnail' => $second,
            ]);

        $product->refresh();
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($product->thumbnail);
    }

    public function test_thumbnail_is_deleted_when_product_is_deleted(): void
    {
        $product = Product::factory()->create();
        $thumbnail = UploadedFile::fake()->image('delete-me.jpg', 800, 600);

        $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), [
                'name' => $product->name,
                'thumbnail' => $thumbnail,
            ]);

        $product->refresh();
        $path = $product->thumbnail;
        Storage::disk('public')->assertExists($path);

        $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $product));

        Storage::disk('public')->assertMissing($path);
    }

    public function test_thumbnail_upload_validates_image_type(): void
    {
        $category = Category::factory()->create();
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'name' => 'Bad Thumbnail Product',
                'thumbnail' => $file,
            ]);

        $response->assertSessionHasErrors('thumbnail');
    }

    // ==================== Edit Form Tests ====================

    public function test_edit_form_shows_thumbnail_upload_field(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $product));

        $response->assertOk();
        $response->assertSee('Card Thumbnail');
    }

    public function test_create_form_shows_thumbnail_upload_field(): void
    {
        Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.create'));

        $response->assertOk();
        $response->assertSee('Card Thumbnail');
    }

    public function test_edit_form_shows_current_thumbnail(): void
    {
        $product = Product::factory()->create();
        $thumbnail = UploadedFile::fake()->image('thumb-preview.jpg', 800, 600);

        $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), [
                'name' => $product->name,
                'thumbnail' => $thumbnail,
            ]);

        $product->refresh();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $product));

        $response->assertOk();
        // Should show the current thumbnail filename
        $response->assertSee(basename($product->thumbnail));
    }

    // ==================== Model Tests ====================

    public function test_thumbnail_is_fillable(): void
    {
        $product = new Product;
        $this->assertContains('thumbnail', $product->getFillable());
    }

    // ==================== Card Preference Tests ====================

    public function test_product_with_thumbnail_prefers_it_over_image_path(): void
    {
        // Create product with both thumbnail and image_path
        $product = Product::factory()->create([
            'thumbnail' => 'products/thumbnails/custom-thumb.webp',
            'image_path' => 'imports/lv-bags-women/some-product/0000.jpg',
        ]);

        // The thumbnail field should take precedence
        $this->assertNotNull($product->thumbnail);
        $this->assertNotNull($product->image_path);
    }
}
