<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use App\Support\Breadcrumbs;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register model observers for cache invalidation
        Product::observe(ProductObserver::class);

        // Set default locale for URL generation so route('home') works
        // even outside locale-prefixed routes (e.g. admin, profile pages).
        // The SetLocale middleware overrides this for locale-prefixed routes.
        URL::defaults(['locale' => 'en']);

        View::composer('*', function ($view) {
            $data = $view->getData();

            // Don't override breadcrumbs that a controller already set
            if (! array_key_exists('breadcrumbs', $data)) {
                $view->with('breadcrumbs', Breadcrumbs::for(request()));
            }
        });
    }
}
