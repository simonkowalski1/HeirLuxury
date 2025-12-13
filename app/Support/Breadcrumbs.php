<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Breadcrumbs
{
    public static function for(Request $request): array
    {
        $route = $request->route();

        if (! $route) {
            return [];
        }

        $name  = $route->getName();
        $items = [];

        // Always start with Home
        $items[] = [
            'label' => 'Home',
            'href'  => route('home'),
        ];

        switch ($name) {

            case 'home':
                break;

            case 'categories.show':
                // Home / {Category Name}

                $slug = $route->parameter('slug');

                $label = Str::headline(str_replace('-', ' ', $slug));

                $items[] = [
                    'label' => $label,
                    'href'  => null,
                ];
                break;

            case 'products.show':
                // Product breadcrumbs come from ProductController
                // but we still provide a fallback
                $items[] = [
                    'label' => 'Product',
                    'href'  => null,
                ];
                break;

            default:
                // Fallback: turn "some.route" into "Some Route"
                $items[] = [
                    'label' => Str::headline(str_replace('.', ' ', $name)),
                    'href'  => null,
                ];
                break;
        }

        return $items;
    }
}
