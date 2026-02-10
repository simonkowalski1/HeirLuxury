<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock Vite to avoid manifest not found errors in tests
        $this->withoutVite();

        $this->admin = User::factory()->create();
        $this->admin->is_admin = true;
        $this->admin->save();

        $this->user = User::factory()->create();
    }

    public function test_dashboard_is_accessible_by_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
    }

    public function test_dashboard_is_not_accessible_by_regular_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_dashboard_is_not_accessible_by_guest(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_dashboard_shows_metrics(): void
    {
        Product::factory()->count(5)->forBrand('Louis Vuitton')->forGender('women')->create();
        Product::factory()->count(3)->forBrand('Chanel')->forGender('men')->create();
        Category::factory()->count(4)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('metrics', function ($metrics) {
            return $metrics['total_products'] === 8
                && $metrics['total_categories'] === 4
                && isset($metrics['products_by_brand'])
                && isset($metrics['products_by_gender']);
        });
    }

    public function test_dashboard_shows_recent_products(): void
    {
        $products = Product::factory()->count(10)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('recentProducts', function ($recentProducts) {
            return $recentProducts->count() === 5;
        });
    }

    public function test_dashboard_shows_products_by_brand(): void
    {
        Product::factory()->count(5)->forBrand('Louis Vuitton')->create();
        Product::factory()->count(3)->forBrand('Chanel')->create();
        Product::factory()->count(2)->forBrand('Dior')->create();

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('metrics', function ($metrics) {
            return isset($metrics['products_by_brand']['Louis Vuitton'])
                && $metrics['products_by_brand']['Louis Vuitton'] === 5;
        });
    }

    public function test_dashboard_shows_products_by_gender(): void
    {
        Product::factory()->count(6)->forGender('women')->create();
        Product::factory()->count(4)->forGender('men')->create();

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('metrics', function ($metrics) {
            return isset($metrics['products_by_gender']['women'])
                && $metrics['products_by_gender']['women'] === 6
                && isset($metrics['products_by_gender']['men'])
                && $metrics['products_by_gender']['men'] === 4;
        });
    }

    public function test_dashboard_handles_empty_database(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('metrics', function ($metrics) {
            return $metrics['total_products'] === 0
                && $metrics['total_categories'] === 0;
        });
        $response->assertViewHas('recentProducts', function ($recentProducts) {
            return $recentProducts->count() === 0;
        });
    }

    public function test_dashboard_renders_total_products_count(): void
    {
        Product::factory()->count(7)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeText('7');
        $response->assertSeeText('Total Products');
    }

    public function test_dashboard_renders_total_categories_count(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeText('3');
        $response->assertSeeText('Total Categories');
    }

    public function test_dashboard_renders_top_brands_list(): void
    {
        Product::factory()->count(5)->forBrand('Louis Vuitton')->create();
        Product::factory()->count(3)->forBrand('Chanel')->create();

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeText('Top Brands');
        $response->assertSeeText('Louis Vuitton');
        $response->assertSeeText('Chanel');
    }

    public function test_dashboard_renders_gender_breakdown(): void
    {
        Product::factory()->count(6)->forGender('women')->create();
        Product::factory()->count(4)->forGender('men')->create();

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeText('Women');
        $response->assertSeeText('Men');
    }

    public function test_dashboard_renders_recent_products_with_edit_links(): void
    {
        $product = Product::factory()->create(['name' => 'Unique Test Bag']);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeText('Recent Products');
        $response->assertSeeText('Unique Test Bag');
        $response->assertSee(route('admin.products.edit', $product));
    }

    public function test_dashboard_renders_empty_state_message(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeText('No products yet');
    }
}
