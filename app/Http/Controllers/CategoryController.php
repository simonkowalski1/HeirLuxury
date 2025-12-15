<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CatalogCache;
use App\Services\CategoryResolver;
use App\Services\ThumbnailService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * Handles catalog browsing with hierarchical category support.
 *
 * This controller manages product listing pages with:
 * - Gender filtering (women, men)
 * - Section filtering (women-bags, men-shoes)
 * - Leaf category filtering (louis-vuitton-women-bags)
 * - Infinite scroll via API endpoint
 *
 * Caching Strategy:
 * - Caches product IDs only (not full models) to reduce memory
 * - Uses versioned cache keys for instant invalidation on product changes
 * - Deterministic pagination: page number used in forPage(), not paginate()
 *
 * @see \App\Services\CategoryResolver For slug resolution logic
 * @see \App\Services\CatalogCache For cache management
 */
class CategoryController extends Controller
{
    protected const PER_PAGE = 24;

    public function __construct(
        protected ThumbnailService $thumbnailService,
        protected CategoryResolver $categoryResolver,
        protected CatalogCache $catalogCache
    ) {}

    /**
     * Display the main catalog page (all products).
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $page = max(1, (int) $request->get('page', 1));

        // Cache product IDs only (lightweight, deterministic)
        $cached = $this->catalogCache->remember('all', $page, function () use ($page) {
            $query = Product::query()->orderBy('id', 'asc');
            $total = $query->count();
            $ids = (clone $query)->forPage($page, self::PER_PAGE)->pluck('id')->all();

            return [
                'ids'       => $ids,
                'total'     => $total,
                'per_page'  => self::PER_PAGE,
                'last_page' => (int) ceil($total / self::PER_PAGE),
            ];
        });

        // Hydrate products from cached IDs
        $products = $this->hydrateProducts($cached['ids'], $page, $cached);

        return view('catalog.categories', [
            'title'    => 'All Categories',
            'products' => $products,
        ]);
    }

    /**
     * Display a category page (filtered by gender, section, or leaf).
     *
     * @param string $category URL slug
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function show(string $category, Request $request)
    {
        $page = max(1, (int) $request->get('page', 1));

        // Resolve URL slug to database category_slug values
        $resolved = $this->categoryResolver->resolve($category);
        $slugsForQuery = $resolved['slugs'];
        $active = $resolved['active'];

        // Create deterministic cache key from sorted slugs
        $slugsHash = $this->categoryResolver->hashSlugs($slugsForQuery);

        // Cache product IDs only (deterministic pagination)
        $cached = $this->catalogCache->remember($slugsHash, $page, function () use ($slugsForQuery, $page) {
            $query = Product::query()
                ->when(count($slugsForQuery) > 0, fn($q) => $q->whereIn('category_slug', $slugsForQuery))
                ->orderBy('id', 'asc');

            $total = $query->count();
            $ids = (clone $query)->forPage($page, self::PER_PAGE)->pluck('id')->all();

            return [
                'ids'       => $ids,
                'total'     => $total,
                'per_page'  => self::PER_PAGE,
                'last_page' => (int) ceil($total / self::PER_PAGE),
            ];
        });

        // Hydrate products from cached IDs
        $products = $this->hydrateProducts($cached['ids'], $page, $cached);

        // Build navigation context
        $slug = Str::of($category)->lower()->slug('-')->toString();
        $catalog = $this->categoryResolver->getCatalog();
        $navGender = $active['gender'] ?? null;

        if (in_array($slug, ['women', 'men'], true)) {
            $navGender = $slug;
        }

        $navCatalog = $navGender && isset($catalog[$navGender]) ? $catalog[$navGender] : $catalog;

        return view('catalog.categories', [
            'slug'       => $slug,
            'title'      => $active['name'],
            'active'     => $active,
            'catalog'    => $catalog,
            'navCatalog' => $navCatalog,
            'products'   => $products,
        ]);
    }

    /**
     * API endpoint for infinite scroll - returns product cards HTML.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiProducts(Request $request)
    {
        $page = max(1, (int) $request->get('page', 1));
        $category = $request->get('category');

        // Resolve category slugs (reuses cached category map)
        $slugsForQuery = [];
        if ($category) {
            $resolved = $this->categoryResolver->resolve($category);
            $slugsForQuery = $resolved['slugs'];
        }

        $slugsHash = $this->categoryResolver->hashSlugs($slugsForQuery);

        // Cache product IDs only (deterministic pagination)
        $cached = $this->catalogCache->remember($slugsHash, $page, function () use ($slugsForQuery, $page) {
            $query = Product::query()
                ->when(count($slugsForQuery) > 0, fn($q) => $q->whereIn('category_slug', $slugsForQuery))
                ->orderBy('id', 'asc');

            $total = $query->count();
            $ids = (clone $query)->forPage($page, self::PER_PAGE)->pluck('id')->all();

            return [
                'ids'       => $ids,
                'total'     => $total,
                'per_page'  => self::PER_PAGE,
                'last_page' => (int) ceil($total / self::PER_PAGE),
            ];
        });

        // Hydrate products from cached IDs (preserving order)
        $products = $this->getProductsByIds($cached['ids']);

        // Render product cards HTML (wrapped in grid cell div to match initial render)
        $html = '';
        foreach ($products as $product) {
            $html .= '<div class="h-full">';
            $html .= view('components.product.card', ['product' => $product])->render();
            $html .= '</div>';
        }

        return response()->json([
            'html'     => $html,
            'hasMore'  => $page < $cached['last_page'],
            'nextPage' => $page + 1,
            'total'    => $cached['total'],
        ]);
    }

    /**
     * Hydrate products from cached IDs into a LengthAwarePaginator.
     *
     * @param array<int> $ids Product IDs in display order
     * @param int $page Current page number
     * @param array $cached Cached pagination metadata
     * @return LengthAwarePaginator
     */
    protected function hydrateProducts(array $ids, int $page, array $cached): LengthAwarePaginator
    {
        $products = $this->getProductsByIds($ids);

        return new LengthAwarePaginator(
            $products,
            $cached['total'],
            $cached['per_page'],
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Load products by IDs, preserving the given order.
     *
     * Uses PHP-based sorting for database-agnostic compatibility
     * (MySQL FIELD() is not supported in SQLite).
     *
     * @param array<int> $ids Product IDs
     * @return \Illuminate\Support\Collection
     */
    protected function getProductsByIds(array $ids)
    {
        if (empty($ids)) {
            return collect();
        }

        // Fetch products and sort in PHP to be database-agnostic
        $products = Product::whereIn('id', $ids)->get();
        $idOrder = array_flip($ids);

        return $products->sortBy(fn($p) => $idOrder[$p->id] ?? PHP_INT_MAX)->values();
    }
}
