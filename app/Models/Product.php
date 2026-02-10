<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a product in the HeirLuxury catalog.
 *
 * Products are luxury items (bags, shoes, clothing, etc.) organized by:
 * - category_slug: The leaf category (e.g., "louis-vuitton-women-bags")
 * - gender: "women" or "men"
 * - section: Product type ("bags", "shoes", "clothes", etc.)
 *
 * Image Storage:
 * - Products have a folder containing multiple images
 * - The 'image' field stores the primary image filename (e.g., "0000.jpg")
 * - The 'image_path' field stores the full relative path for quick access
 * - Full path: storage/imports/{base-folder}/{product-folder}/
 *
 * URL Structure:
 * - Products are accessed via: /categories/{category_slug}/{slug}
 * - Example: /categories/louis-vuitton-women-bags/neverfull-mm
 *
 * Database Indexes:
 * - category_slug: For category filtering
 * - (category_slug, slug): For unique product lookup
 * - brand: For brand filtering
 * - gender: For gender filtering
 *
 * @property int $id
 * @property string $name Product display name (also used as folder name)
 * @property string $slug URL-friendly version of name
 * @property string $category_slug The category this product belongs to
 * @property string $gender "women" or "men"
 * @property string|null $brand Brand name (if applicable)
 * @property string $section Product type (bags, shoes, clothes, etc.)
 * @property string $folder Name of the image folder in storage
 * @property string $image Primary image filename
 * @property string|null $image_path Full relative path to primary image
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @see \App\Console\Commands\ImportLV For how products are imported
 * @see \App\Http\Controllers\ProductController For product display logic
 */
class Product extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'slug',
        'category_slug',
        'gender',
        'brand',
        'section',
        'folder',
        'image',
        'image_path',
    ];

    /**
     * Get the category this product belongs to.
     *
     * Note: This relationship assumes a Category model exists with 'slug'
     * as the primary key. Currently categories are defined in config/categories.php
     * rather than in the database.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_slug', 'slug');
    }

    /**
     * Get all gallery images for this product, ordered by sort position.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary gallery image for this product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }
}
