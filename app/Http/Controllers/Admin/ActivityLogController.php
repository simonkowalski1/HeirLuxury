<?php

// ABOUTME: Admin controller for viewing the activity audit log.
// ABOUTME: Displays filterable, paginated history of admin actions on tracked models.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display paginated activity log with optional filters.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderByDesc('created_at');

        // Filter by action type
        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        // Filter by model type
        if ($modelType = $request->get('model_type')) {
            $query->where('model_type', $modelType);
        }

        $logs = $query->paginate(30)->withQueryString();

        // Distinct model types for the filter dropdown
        $modelTypes = ActivityLog::distinct()->pluck('model_type')
            ->map(fn ($type) => ['value' => $type, 'label' => class_basename($type)])
            ->sortBy('label')
            ->values();

        return view('admin.activity-log.index', compact('logs', 'modelTypes'));
    }
}
