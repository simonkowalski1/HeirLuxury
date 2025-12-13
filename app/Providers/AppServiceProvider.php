<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Support\Breadcrumbs;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $data = $view->getData();

            // Don't override breadcrumbs that a controller already set
            if (! array_key_exists('breadcrumbs', $data)) {
                $view->with('breadcrumbs', Breadcrumbs::for(request()));
            }
        });
    }
}
