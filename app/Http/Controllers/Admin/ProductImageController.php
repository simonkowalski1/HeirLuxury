<?php

// ABOUTME: Admin controller for managing product gallery images.
// ABOUTME: Handles upload, delete, reorder, and primary image selection.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Upload one or more images to a product's gallery.
     *
     * Images are stored in products/gallery/{product_id}/ and assigned
     * sequential sort orders. The first image uploaded becomes primary
     * if no primary exists yet.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'images' => ['required', 'array'],
            'images.*' => ['image', 'max:4096'],
        ]);

        $maxOrder = $product->images()->max('sort_order') ?? -1;
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        foreach ($request->file('images') as $index => $file) {
            $path = $file->store("products/gallery/{$product->id}", 'public');

            $product->images()->create([
                'path' => $path,
                'alt_text' => $product->name,
                'sort_order' => $maxOrder + $index + 1,
                'is_primary' => ! $hasPrimary && $index === 0,
            ]);
        }

        return redirect()->route('admin.products.edit', $product)
            ->with('status', 'Images uploaded.');
    }

    /**
     * Delete a single gallery image.
     *
     * Removes the file from storage and the database record.
     * If the deleted image was primary, promotes the next image in sort order.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product, ProductImage $image)
    {
        $wasPrimary = $image->is_primary;

        // Remove file from storage
        Storage::disk('public')->delete($image->path);

        $image->delete();

        // If deleted image was primary, promote the next image
        if ($wasPrimary) {
            $nextImage = $product->images()->orderBy('sort_order')->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return redirect()->route('admin.products.edit', $product)
            ->with('status', 'Image deleted.');
    }

    /**
     * Reorder gallery images by updating their sort_order values.
     *
     * Expects an array of image IDs in the desired display order.
     * The first image in the order is set as primary.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request, Product $product)
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        $order = $request->input('order');

        // Reset all images for this product to non-primary
        $product->images()->update(['is_primary' => false]);

        foreach ($order as $index => $imageId) {
            $product->images()
                ->where('id', $imageId)
                ->update([
                    'sort_order' => $index,
                    'is_primary' => $index === 0,
                ]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Set a specific image as the primary image for its product.
     *
     * Clears primary flag from all other images on the same product.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setPrimary(Product $product, ProductImage $image)
    {
        // Clear primary from all images on this product
        $product->images()->update(['is_primary' => false]);

        // Set the selected image as primary
        $image->update(['is_primary' => true]);

        return redirect()->route('admin.products.edit', $product)
            ->with('status', 'Primary image updated.');
    }
}
