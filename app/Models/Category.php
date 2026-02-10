<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a product category in the database.
 *
 * Note: Currently, categories are primarily defined in config/categories.php
 * for navigation purposes. This model exists for potential future use where
 * categories might be managed dynamically through an admin interface.
 *
 * The Product model references categories via 'category_slug', which maps
 * to this model's 'slug' field.
 *
 * @property int $id
 * @property string $name Category display name
 * @property string $slug URL-friendly identifier (e.g., "louis-vuitton-women-bags")
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @see config/categories.php For the primary category taxonomy
 * @see \App\Models\Product For the relationship back to products
 */
class Category extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
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
}
