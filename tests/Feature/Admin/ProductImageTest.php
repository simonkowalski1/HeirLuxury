<?php

// ABOUTME: Feature tests for multi-image gallery management in admin panel.
// ABOUTME: Covers upload, reorder, delete, and primary image selection for product images.

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        Storage::fake('public');

        $this->admin = User::factory()->create();
        $this->admin->is_admin = true;
        $this->admin->save();

        $this->product = Product::factory()->create();
    }

    // ==================== Upload Tests ====================

    public function test_gallery_images_can_be_uploaded(): void
    {
        $files = [
            UploadedFile::fake()->image('front.jpg'),
            UploadedFile::fake()->image('back.jpg'),
            UploadedFile::fake()->image('side.jpg'),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.images.store', $this->product), [
                'images' => $files,
            ]);

        $response->assertRedirect();
        $this->assertCount(3, $this->product->fresh()->images);
    }

    public function test_uploaded_images_are_stored_on_disk(): void
    {
        $file = UploadedFile::fake()->image('product-photo.jpg');

        $this->actingAs($this->admin)
            ->post(route('admin.products.images.store', $this->product), [
                'images' => [$file],
            ]);

        $image = $this->product->fresh()->images->first();
        Storage::disk('public')->assertExists($image->path);
    }

    public function test_uploaded_images_get_sequential_sort_order(): void
    {
        $files = [
            UploadedFile::fake()->image('first.jpg'),
            UploadedFile::fake()->image('second.jpg'),
            UploadedFile::fake()->image('third.jpg'),
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.products.images.store', $this->product), [
                'images' => $files,
            ]);

        $images = $this->product->fresh()->images()->orderBy('sort_order')->get();
        $this->assertEquals(0, $images[0]->sort_order);
        $this->assertEquals(1, $images[1]->sort_order);
        $this->assertEquals(2, $images[2]->sort_order);
    }

    public function test_first_uploaded_image_is_primary(): void
    {
        $files = [
            UploadedFile::fake()->image('primary.jpg'),
            UploadedFile::fake()->image('secondary.jpg'),
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.products.images.store', $this->product), [
                'images' => $files,
            ]);

        $images = $this->product->fresh()->images()->orderBy('sort_order')->get();
        $this->assertTrue($images[0]->is_primary);
        $this->assertFalse($images[1]->is_primary);
    }

    public function test_upload_appends_to_existing_gallery(): void
    {
        // Upload first batch
        $this->actingAs($this->admin)
            ->post(route('admin.products.images.store', $this->product), [
                'images' => [UploadedFile::fake()->image('existing.jpg')],
            ]);

        // Upload second batch
        $this->actingAs($this->admin)
            ->post(route('admin.products.images.store', $this->product), [
                'images' => [UploadedFile::fake()->image('new.jpg')],
            ]);

        $this->assertCount(2, $this->product->fresh()->images);
    }

    public function test_upload_validates_image_files(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.images.store', $this->product), [
                'images' => [$file],
            ]);

        $response->assertSessionHasErrors('images.0');
        $this->assertCount(0, $this->product->fresh()->images);
    }

    // ==================== Delete Tests ====================

    public function test_gallery_image_can_be_deleted(): void
    {
        $file = UploadedFile::fake()->image('delete-me.jpg');

        $this->actingAs($this->admin)
            ->post(route('admin.products.images.store', $this->product), [
                'images' => [$file],
            ]);

        $image = $this->product->fresh()->images->first();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.products.images.destroy', [$this->product, $image]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('product_images', ['id' => $image->id]);
    }

    public function test_deleted_image_file_is_removed_from_storage(): void
    {
        $file = UploadedFile::fake()->image('remove-file.jpg');

        $this->actingAs($this->admin)
            ->post(route('admin.products.images.store', $this->product), [
                'images' => [$file],
            ]);

        $image = $this->product->fresh()->images->first();
        $path = $image->path;

        Storage::disk('public')->assertExists($path);

        $this->actingAs($this->admin)
            ->delete(route('admin.products.images.destroy', [$this->product, $image]));

        Storage::disk('public')->assertMissing($path);
    }

    public function test_deleting_primary_image_promotes_next_image(): void
    {
        $files = [
            UploadedFile::fake()->image('primary.jpg'),
            UploadedFile::fake()->image('secondary.jpg'),
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.products.images.store', $this->product), [
                'images' => $files,
            ]);

        $images = $this->product->fresh()->images()->orderBy('sort_order')->get();
        $primaryImage = $images[0];
        $secondaryImage = $images[1];

        $this->actingAs($this->admin)
            ->delete(route('admin.products.images.destroy', [$this->product, $primaryImage]));

        $this->assertTrue($secondaryImage->fresh()->is_primary);
    }

    // ==================== Reorder Tests ====================

    public function test_gallery_images_can_be_reordered(): void
    {
        $files = [
            UploadedFile::fake()->image('a.jpg'),
            UploadedFile::fake()->image('b.jpg'),
            UploadedFile::fake()->image('c.jpg'),
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.products.images.store', $this->product), [
                'images' => $files,
            ]);

        $images = $this->product->fresh()->images()->orderBy('sort_order')->get();

        // Reverse the order
        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.images.reorder', $this->product), [
                'order' => [$images[2]->id, $images[1]->id, $images[0]->id],
            ]);

        $response->assertOk();

        $reordered = $this->product->fresh()->images()->orderBy('sort_order')->get();
        $this->assertEquals($images[2]->id, $reordered[0]->id);
        $this->assertEquals($images[1]->id, $reordered[1]->id);
        $this->assertEquals($images[0]->id, $reordered[2]->id);
    }

    public function test_reorder_sets_first_image_as_primary(): void
    {
        $files = [
            UploadedFile::fake()->image('a.jpg'),
            UploadedFile::fake()->image('b.jpg'),
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.products.images.store', $this->product), [
                'images' => $files,
            ]);

        $images = $this->product->fresh()->images()->orderBy('sort_order')->get();

        // Make second image first
        $this->actingAs($this->admin)
            ->put(route('admin.products.images.reorder', $this->product), [
                'order' => [$images[1]->id, $images[0]->id],
            ]);

        $this->assertTrue($images[1]->fresh()->is_primary);
        $this->assertFalse($images[0]->fresh()->is_primary);
    }

    // ==================== Set Primary Tests ====================

    public function test_image_can_be_set_as_primary(): void
    {
        $files = [
            UploadedFile::fake()->image('first.jpg'),
            UploadedFile::fake()->image('second.jpg'),
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.products.images.store', $this->product), [
                'images' => $files,
            ]);

        $images = $this->product->fresh()->images()->orderBy('sort_order')->get();
        $secondImage = $images[1];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.images.primary', [$this->product, $secondImage]));

        $response->assertRedirect();

        $this->assertTrue($secondImage->fresh()->is_primary);
        $this->assertFalse($images[0]->fresh()->is_primary);
    }

    // ==================== Model Relationship Tests ====================

    public function test_product_has_many_images(): void
    {
        ProductImage::factory()->count(3)->for($this->product)->create();

        $this->assertCount(3, $this->product->fresh()->images);
        $this->assertInstanceOf(ProductImage::class, $this->product->images->first());
    }

    public function test_product_image_belongs_to_product(): void
    {
        $image = ProductImage::factory()->for($this->product)->create();

        $this->assertInstanceOf(Product::class, $image->product);
        $this->assertEquals($this->product->id, $image->product->id);
    }

    public function test_product_primary_image_returns_primary(): void
    {
        ProductImage::factory()->for($this->product)->create(['is_primary' => false, 'sort_order' => 1]);
        $primary = ProductImage::factory()->for($this->product)->create(['is_primary' => true, 'sort_order' => 0]);

        $this->assertEquals($primary->id, $this->product->fresh()->primaryImage->id);
    }

    public function test_product_images_are_ordered_by_sort_order(): void
    {
        ProductImage::factory()->for($this->product)->create(['sort_order' => 2]);
        ProductImage::factory()->for($this->product)->create(['sort_order' => 0]);
        ProductImage::factory()->for($this->product)->create(['sort_order' => 1]);

        $images = $this->product->fresh()->images;
        $this->assertEquals(0, $images[0]->sort_order);
        $this->assertEquals(1, $images[1]->sort_order);
        $this->assertEquals(2, $images[2]->sort_order);
    }

    // ==================== Admin Form Tests ====================

    public function test_edit_form_shows_gallery_section(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $this->product));

        $response->assertOk();
        $response->assertSee('Image Gallery');
    }

    public function test_edit_form_shows_existing_gallery_images(): void
    {
        $image = ProductImage::factory()->for($this->product)->create([
            'path' => 'products/gallery/test-image.jpg',
        ]);

        // Put a file there so the URL resolves
        Storage::disk('public')->put('products/gallery/test-image.jpg', 'fake-image-content');

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $this->product));

        $response->assertOk();
        $response->assertSee('test-image.jpg');
    }

    // ==================== Cleanup Tests ====================

    public function test_deleting_product_deletes_gallery_images(): void
    {
        $file = UploadedFile::fake()->image('gallery-image.jpg');

        $this->actingAs($this->admin)
            ->post(route('admin.products.images.store', $this->product), [
                'images' => [$file],
            ]);

        $image = $this->product->fresh()->images->first();
        $path = $image->path;
        $imageId = $image->id;

        Storage::disk('public')->assertExists($path);

        $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $this->product));

        $this->assertDatabaseMissing('product_images', ['id' => $imageId]);
        Storage::disk('public')->assertMissing($path);
    }
}
