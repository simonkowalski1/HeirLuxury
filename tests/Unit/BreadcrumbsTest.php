<?php

// ABOUTME: Unit tests for the Breadcrumbs support class.
// ABOUTME: Verifies breadcrumb generation for various route types.

namespace Tests\Unit;

use App\Support\Breadcrumbs;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Tests\TestCase;

class BreadcrumbsTest extends TestCase
{
    public function test_returns_empty_array_when_no_route(): void
    {
        $request = Request::create('/');

        $breadcrumbs = Breadcrumbs::for($request);

        $this->assertEquals([], $breadcrumbs);
    }

    public function test_home_route_returns_only_home_breadcrumb(): void
    {
        $request = Request::create('/en');
        $route = new Route('GET', '{locale}', fn () => null);
        $route->name('home');
        $route->bind($request);

        $request->setRouteResolver(fn () => $route);

        $breadcrumbs = Breadcrumbs::for($request);

        $this->assertCount(1, $breadcrumbs);
        $this->assertEquals('Home', $breadcrumbs[0]['label']);
        $this->assertNotNull($breadcrumbs[0]['href']);
    }

    public function test_default_route_generates_headline_from_route_name(): void
    {
        $request = Request::create('/admin/dashboard');
        $route = new Route('GET', 'admin/dashboard', fn () => null);
        $route->name('admin.dashboard');
        $route->bind($request);

        $request->setRouteResolver(fn () => $route);

        $breadcrumbs = Breadcrumbs::for($request);

        $this->assertCount(2, $breadcrumbs);
        $this->assertEquals('Home', $breadcrumbs[0]['label']);
        $this->assertEquals('Admin Dashboard', $breadcrumbs[1]['label']);
        $this->assertNull($breadcrumbs[1]['href']);
    }

    public function test_home_breadcrumb_always_has_href(): void
    {
        $request = Request::create('/en/some-page');
        $route = new Route('GET', '{locale}/some-page', fn () => null);
        $route->name('some.page');
        $route->bind($request);

        $request->setRouteResolver(fn () => $route);

        $breadcrumbs = Breadcrumbs::for($request);

        $this->assertNotNull($breadcrumbs[0]['href']);
        $this->assertStringContainsString('/en', $breadcrumbs[0]['href']);
    }
}
