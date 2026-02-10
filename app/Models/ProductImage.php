<?php

// ABOUTME: Represents a single image in a product's gallery.
// ABOUTME: Tracks path, sort order, alt text, and whether this is the primary image.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'product_id',
        'path',
        'alt_text',
        'sort_order',
        'is_primary',
    ];

    /**
     * Attribute casts.
     */
    protected $casts = [
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    /**
     * Get the product this image belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
