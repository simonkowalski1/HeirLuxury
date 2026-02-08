<?php

// ABOUTME: Handles guest-only session-based wishlist functionality.
// ABOUTME: Provides toggle, count, ids, and items API endpoints.

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    private const SESSION_KEY = 'wishlist_ids';

    public function toggle(Request $request, Product $product): JsonResponse
    {
        $ids = $request->session()->get(self::SESSION_KEY, []);

        if (in_array($product->id, $ids)) {
            $ids = array_values(array_diff($ids, [$product->id]));
            $wishlisted = false;
        } else {
            $ids[] = $product->id;
            $wishlisted = true;
        }

        $request->session()->put(self::SESSION_KEY, $ids);

        return response()->json([
            'wishlisted' => $wishlisted,
            'count' => count($ids),
        ]);
    }

    public function count(Request $request): JsonResponse
    {
        $ids = $request->session()->get(self::SESSION_KEY, []);

        return response()->json([
            'count' => count($ids),
        ]);
    }

    public function ids(Request $request): JsonResponse
    {
        $ids = $request->session()->get(self::SESSION_KEY, []);

        return response()->json([
            'ids' => $ids,
        ]);
    }

    public function items(Request $request): JsonResponse
    {
        $ids = $request->session()->get(self::SESSION_KEY, []);

        if (empty($ids)) {
            return response()->json(['items' => []]);
        }

        $products = Product::whereIn('id', $ids)
            ->get(['id', 'name', 'slug', 'category_slug', 'brand', 'image_path']);

        return response()->json([
            'items' => $products,
        ]);
    }
}
