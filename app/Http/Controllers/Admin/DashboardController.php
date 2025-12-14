<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

/**
 * Admin panel dashboard controller.
 *
 * Provides the main entry point for the admin area.
 * The dashboard view can be extended to show statistics,
 * recent activity, or quick actions.
 *
 * Access: Requires authentication + admin middleware.
 *
 * @see routes/web.php For route definition (admin.dashboard)
 */
class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.dashboard');
    }
}
