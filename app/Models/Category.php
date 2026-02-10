<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a product category in the database.
 *
 * Categories are the single source of truth for catalog navigation (mega menu,
 * sidenav) and product organization. Each category has a gender, section, and
 * brand that determines where it appears in the UI.
 *
 * The Product model references categories via 'category_slug', which maps
 * to this model's 'slug' field.
 *
 * @property int $id
 * @property string $name Category display name (e.g., "Louis Vuitton Women Bags")
 * @property string $slug URL-friendly identifier (e.g., "louis-vuitton-women-bags")
 * @property string|null $gender "women" or "men"
 * @property string|null $section Product type: "bags", "shoes", "clothing", "belts", "jewelry", "glasses"
 * @property string|null $brand Brand name (e.g., "Louis Vuitton")
 * @property int $display_order Sort order for navigation display
 * @property bool $is_active Whether this category appears in navigation
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @see \App\Models\Product For the relationship back to products
 */
class Category extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'gender',
        'section',
        'brand',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the products that belong to this category.
     *
     * Products reference categories via category_slug → slug.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_slug', 'slug');
    }

    /**
     * Scope to active categories only.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get categories grouped by gender and section for navigation.
     *
     * Returns the same structure as config/categories.php so existing
     * views (mega menu, sidenav) work without modification.
     *
     * @return array{women: array, men: array}
     */
    public static function getNavigationData(): array
    {
        $categories = static::active()
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $result = ['women' => [], 'men' => []];

        foreach ($categories as $category) {
            if (! $category->gender || ! $category->section) {
                continue;
            }

            $sectionLabel = ucfirst($category->section);

            $result[$category->gender][$sectionLabel][] = [
                'name' => $category->name,
                'route' => 'catalog.category',
                'params' => ['category' => $category->slug],
            ];
        }

        return $result;
    }
}
