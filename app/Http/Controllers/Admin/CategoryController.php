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
 * Categories drive the frontend navigation (mega menu, sidenav)
 * through the Category::getNavigationData() method.
 *
 * Access: Requires authentication + admin middleware.
 *
 * @see \App\Models\Category For the Category model
 */
class CategoryController extends Controller
{
    /**
     * Display paginated list of categories with search, filter, and sort.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        // Search by name or slug
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filter by product presence (uses subquery to avoid SQLite HAVING issues)
        if ($request->has('has_products') && $request->get('has_products') !== '') {
            if ($request->get('has_products') === '1') {
                $query->whereHas('products');
            } else {
                $query->whereDoesntHave('products');
            }
        }

        // Sortable columns (whitelist to prevent SQL injection)
        $sortableColumns = ['name', 'slug', 'products_count', 'created_at'];
        $sort = in_array($request->get('sort'), $sortableColumns) ? $request->get('sort') : 'name';
        $direction = $request->get('direction') === 'desc' ? 'desc' : 'asc';

        $categories = $query->orderBy($sort, $direction)->paginate(20)->withQueryString();

        return view('admin.categories.index', compact('categories', 'sort', 'direction'));
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
            'gender' => ['nullable', 'string', 'in:women,men'],
            'section' => ['nullable', 'string', 'max:50'],
            'brand' => ['nullable', 'string', 'max:100'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable'],
        ]);

        // Auto-generate slug from name if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Checkbox: present = active, absent = inactive
        $data['is_active'] = $request->has('is_active');

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
            'gender' => ['nullable', 'string', 'in:women,men'],
            'section' => ['nullable', 'string', 'max:50'],
            'brand' => ['nullable', 'string', 'max:100'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable'],
        ]);

        // Auto-generate slug from name if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Checkbox: present = active, absent = inactive
        $data['is_active'] = $request->has('is_active');

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
