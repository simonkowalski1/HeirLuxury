<?php

// ABOUTME: Feature tests for the activity audit log system.
// ABOUTME: Covers auto-logging on model events and the admin activity log view.

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->admin = User::factory()->create();
        $this->admin->is_admin = true;
        $this->admin->save();
    }

    // ==================== Auto-Logging on Model Events ====================

    public function test_creating_product_logs_activity(): void
    {
        $this->actingAs($this->admin);

        Product::create([
            'name' => 'Test Bag',
            'slug' => 'test-bag',
            'category_slug' => 'women-bags',
            'gender' => 'women',
            'brand' => 'Gucci',
            'section' => 'bags',
            'folder' => 'test-folder',
            'image' => '0000.jpg',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'created',
            'model_type' => Product::class,
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_updating_product_logs_activity_with_changes(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'name' => 'Old Name',
            'slug' => 'old-name',
            'category_slug' => 'women-bags',
            'gender' => 'women',
            'brand' => 'Gucci',
            'section' => 'bags',
            'folder' => 'test-folder',
            'image' => '0000.jpg',
        ]);

        $product->update(['name' => 'New Name']);

        $log = ActivityLog::where('action', 'updated')
            ->where('model_type', Product::class)
            ->first();

        $this->assertNotNull($log);
        $this->assertArrayHasKey('name', $log->changes);
        $this->assertEquals('Old Name', $log->changes['name']['old']);
        $this->assertEquals('New Name', $log->changes['name']['new']);
    }

    public function test_deleting_product_logs_activity(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'name' => 'To Delete',
            'slug' => 'to-delete',
            'category_slug' => 'women-bags',
            'gender' => 'women',
            'brand' => 'Gucci',
            'section' => 'bags',
            'folder' => 'test-folder',
            'image' => '0000.jpg',
        ]);

        $productId = $product->id;
        $product->delete();

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'deleted',
            'model_type' => Product::class,
            'model_id' => $productId,
        ]);
    }

    public function test_creating_category_logs_activity(): void
    {
        $this->actingAs($this->admin);

        Category::create(['name' => 'Test Cat', 'slug' => 'test-cat']);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'created',
            'model_type' => Category::class,
        ]);
    }

    public function test_updating_category_logs_activity_with_changes(): void
    {
        $this->actingAs($this->admin);

        $category = Category::create(['name' => 'Old Cat', 'slug' => 'old-cat']);
        $category->update(['name' => 'New Cat']);

        $log = ActivityLog::where('action', 'updated')
            ->where('model_type', Category::class)
            ->first();

        $this->assertNotNull($log);
        $this->assertArrayHasKey('name', $log->changes);
        $this->assertEquals('Old Cat', $log->changes['name']['old']);
        $this->assertEquals('New Cat', $log->changes['name']['new']);
    }

    public function test_deleting_category_logs_activity(): void
    {
        $this->actingAs($this->admin);

        $category = Category::create(['name' => 'Del Cat', 'slug' => 'del-cat']);
        $categoryId = $category->id;
        $category->delete();

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'deleted',
            'model_type' => Category::class,
            'model_id' => $categoryId,
        ]);
    }

    public function test_activity_log_records_user_id(): void
    {
        $this->actingAs($this->admin);

        Category::create(['name' => 'User Test', 'slug' => 'user-test']);

        $log = ActivityLog::first();
        $this->assertEquals($this->admin->id, $log->user_id);
    }

    public function test_activity_log_skips_updated_at_only_changes(): void
    {
        $this->actingAs($this->admin);

        $category = Category::create(['name' => 'No Change', 'slug' => 'no-change']);

        // Clear the "created" log
        ActivityLog::truncate();

        // Touch the model (only updates updated_at)
        $category->touch();

        // Should not create an "updated" log since only updated_at changed
        $this->assertDatabaseMissing('activity_logs', [
            'action' => 'updated',
            'model_type' => Category::class,
        ]);
    }

    // ==================== Activity Log View ====================

    public function test_activity_log_index_accessible_by_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.activity-log.index'));

        $response->assertOk();
    }

    public function test_activity_log_index_not_accessible_by_guest(): void
    {
        $response = $this->get(route('admin.activity-log.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_activity_log_index_not_accessible_by_regular_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.activity-log.index'));

        $response->assertForbidden();
    }

    public function test_activity_log_index_displays_log_entries(): void
    {
        $this->actingAs($this->admin);

        Category::create(['name' => 'Logged Category', 'slug' => 'logged-cat']);

        $response = $this->actingAs($this->admin)->get(route('admin.activity-log.index'));

        $response->assertOk();
        $response->assertSee('created');
        $response->assertSee('Category');
    }

    public function test_activity_log_index_shows_newest_first(): void
    {
        $this->actingAs($this->admin);

        Category::create(['name' => 'First Cat', 'slug' => 'first-cat']);
        Category::create(['name' => 'Second Cat', 'slug' => 'second-cat']);

        $response = $this->actingAs($this->admin)->get(route('admin.activity-log.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Second Cat', 'First Cat']);
    }

    public function test_activity_log_filter_by_action(): void
    {
        $this->actingAs($this->admin);

        $cat = Category::create(['name' => 'Filter Cat', 'slug' => 'filter-cat']);
        $cat->update(['name' => 'Updated Cat']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.activity-log.index', ['action' => 'updated']));

        $response->assertOk();
        $response->assertSee('updated');
    }

    public function test_activity_log_filter_by_model_type(): void
    {
        $this->actingAs($this->admin);

        Category::create(['name' => 'Cat Log', 'slug' => 'cat-log']);
        Product::create([
            'name' => 'Prod Log', 'slug' => 'prod-log',
            'category_slug' => 'cat-log', 'gender' => 'women',
            'brand' => 'Test', 'section' => 'bags',
            'folder' => 'f', 'image' => 'i.jpg',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.activity-log.index', ['model_type' => Category::class]));

        $response->assertOk();
        $response->assertSee('Cat Log');
    }

    public function test_activity_log_paginates_results(): void
    {
        $this->actingAs($this->admin);

        // Create 35 categories to generate 35 log entries
        for ($i = 0; $i < 35; $i++) {
            Category::create(['name' => "Cat {$i}", 'slug' => "cat-{$i}"]);
        }

        $response = $this->actingAs($this->admin)->get(route('admin.activity-log.index'));

        $response->assertOk();
        $response->assertViewHas('logs', function ($logs) {
            return $logs->count() === 30; // 30 per page
        });
    }

    public function test_activity_log_shows_user_who_performed_action(): void
    {
        $this->actingAs($this->admin);

        Category::create(['name' => 'User Action', 'slug' => 'user-action']);

        $response = $this->actingAs($this->admin)->get(route('admin.activity-log.index'));

        $response->assertOk();
        $response->assertSee($this->admin->email);
    }

    public function test_activity_log_sidebar_link_exists(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Activity Log');
        $response->assertSee(route('admin.activity-log.index'));
    }
}
