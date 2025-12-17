<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

/**
 * Admin panel dashboard controller.
 *
 * Provides the main entry point for the admin area with metrics
 * and recent activity overview.
 *
 * Access: Requires authentication + admin middleware.
 *
 * @see routes/web.php For route definition (admin.dashboard)
 */
class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with metrics.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $metrics = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'products_by_brand' => Product::select('brand', DB::raw('count(*) as count'))
                ->whereNotNull('brand')
                ->groupBy('brand')
                ->orderByDesc('count')
                ->limit(5)
                ->pluck('count', 'brand')
                ->toArray(),
            'products_by_gender' => Product::select('gender', DB::raw('count(*) as count'))
                ->whereNotNull('gender')
                ->groupBy('gender')
                ->pluck('count', 'gender')
                ->toArray(),
        ];

        $recentProducts = Product::latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('metrics', 'recentProducts'));
    }
}
