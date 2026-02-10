<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Admin controller for managing categories.
 *
 * Provides CRUD operations for categories stored in the database.
 *
 * Note: The primary category taxonomy is defined in config/categories.php.
 * Categories created here can be used for dynamic management but
 * navigation currently relies on the config file.
 *
 * To integrate database categories with navigation:
 * 1. Create categories here with matching slugs
 * 2. Update config/categories.php to reference these slugs
 *
 * Access: Requires authentication + admin middleware.
 *
 * @see \App\Models\Category For the Category model
 * @see config/categories.php For the navigation taxonomy
 */
class CategoryController extends Controller
{
    /**
     * Display paginated list of all categories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the category creation form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a new category in the database.
     *
     * Validates input and auto-generates slug if not provided.
     * Slug must be unique across all categories.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
        ]);

        // Auto-generate slug from name if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category created.');
    }

    /**
     * Show the category edit form.
     *
     * @param  Category  $category  Route model binding
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update an existing category.
     *
     * Slug uniqueness is validated excluding the current category.
     *
     * @param  Category  $category  Route model binding
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug,'.$category->id],
        ]);

        // Auto-generate slug from name if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category updated.');
    }

    /**
     * Delete a category.
     *
     * Warning: This does not cascade to products. Products referencing
     * this category's slug will still have that category_slug value.
     *
     * @param  Category  $category  Route model binding
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category deleted.');
    }
}
