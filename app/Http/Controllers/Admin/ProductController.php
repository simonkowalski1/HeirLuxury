<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Admin controller for managing products.
 *
 * Provides CRUD operations for products in the admin panel.
 * Products created here are stored separately from bulk-imported products
 * (which use the ImportLV command).
 *
 * Note: For bulk product management, consider using:
 * - php artisan import:lv (bulk import from folders)
 * - php artisan products:backfill-slugs (regenerate slugs)
 *
 * Access: Requires authentication + admin middleware.
 *
 * @see \App\Models\Product For the Product model
 * @see \App\Console\Commands\ImportLV For bulk imports
 */
class ProductController extends Controller
{
    /**
     * Display paginated list of all products.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the product creation form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a new product in the database.
     *
     * Validates input, auto-generates slug if not provided,
     * and handles image upload to storage/app/public/products.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'slug'          => ['nullable', 'string', 'max:255'],
            'category_slug' => ['nullable', 'string', 'max:255'],
            'gender'        => ['nullable', 'string', 'max:50'],
            'brand'         => ['nullable', 'string', 'max:100'],
            'section'       => ['nullable', 'string', 'max:100'],
            'folder'        => ['nullable', 'string', 'max:255'],
            'image'         => ['nullable', 'image', 'max:4096'],
        ]);

        // Auto-generate slug from name if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('status', 'Product created.');
    }

    /**
     * Show the product edit form.
     *
     * @param Product $product Route model binding
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update an existing product.
     *
     * Handles image replacement (deletes old image when new one uploaded).
     *
     * @param Request $request
     * @param Product $product Route model binding
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'slug'          => ['nullable', 'string', 'max:255'],
            'category_slug' => ['nullable', 'string', 'max:255'],
            'gender'        => ['nullable', 'string', 'max:50'],
            'brand'         => ['nullable', 'string', 'max:100'],
            'section'       => ['nullable', 'string', 'max:100'],
            'folder'        => ['nullable', 'string', 'max:255'],
            'image'         => ['nullable', 'image', 'max:4096'],
        ]);

        // Auto-generate slug from name if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Handle image replacement
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')
                ->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('status', 'Product updated.');
    }

    /**
     * Delete a product and its associated image.
     *
     * @param Product $product Route model binding
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        // Clean up associated image file
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('status', 'Product deleted.');
    }
}
