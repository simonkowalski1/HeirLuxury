# HeirLuxury Architecture

This document provides detailed technical documentation for the HeirLuxury luxury product catalog application.

## Table of Contents

1. [System Overview](#system-overview)
2. [Directory Structure](#directory-structure)
3. [Data Models](#data-models)
4. [Services](#services)
5. [Caching Strategy](#caching-strategy)
6. [Image Pipeline](#image-pipeline)
7. [Category System](#category-system)
8. [Request Flow](#request-flow)
9. [Security](#security)
10. [Testing](#testing)

---

## System Overview

HeirLuxury is a Laravel 12 application designed to showcase luxury products from multiple high-end brands. The architecture prioritizes:

- **Performance**: Aggressive caching with instant invalidation
- **Scalability**: Stateless design supporting horizontal scaling
- **Maintainability**: Clear separation of concerns via services
- **Image Optimization**: On-demand WebP thumbnail generation

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         Browser                                  │
├─────────────────────────────────────────────────────────────────┤
│                    Vite (Dev) / CDN (Prod)                      │
│                   CSS, JS, Static Assets                         │
├─────────────────────────────────────────────────────────────────┤
│                      Laravel Application                         │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐  │
│  │ Controllers │→ │  Services   │→ │    Cache Layer          │  │
│  │             │  │             │  │  (File/Redis)           │  │
│  └─────────────┘  └─────────────┘  └─────────────────────────┘  │
│         │                │                      │                │
│         ▼                ▼                      ▼                │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐  │
│  │   Models    │  │ Filesystem  │  │       SQLite/MySQL      │  │
│  │  (Eloquent) │  │  (Storage)  │  │       Database          │  │
│  └─────────────┘  └─────────────┘  └─────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

---

## Directory Structure

```
HeirLuxury/
├── app/
│   ├── Console/Commands/           # CLI commands
│   │   ├── ImportLV.php           # Product import from folders
│   │   ├── ImportBrands.php       # Multi-brand import
│   │   ├── GenerateThumbnails.php # Batch thumbnail generation
│   │   ├── BackfillSlugs.php      # Slug backfill utility
│   │   └── BackfillProductSlugs.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CategoryController.php  # Catalog listing pages
│   │   │   ├── ProductController.php   # Product detail pages
│   │   │   ├── HomeController.php      # Landing page
│   │   │   ├── InquiryController.php   # Contact forms
│   │   │   └── Admin/                  # Admin panel
│   │   │       ├── DashboardController.php
│   │   │       ├── ProductController.php
│   │   │       └── CategoryController.php
│   │   │
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php     # Admin role check
│   │       ├── SecurityHeaders.php     # HTTP security headers
│   │       └── TrustHosts.php          # Host validation
│   │
│   ├── Models/
│   │   ├── Product.php      # Luxury product entity
│   │   ├── Category.php     # Product category
│   │   └── User.php         # Admin users
│   │
│   ├── Observers/
│   │   └── ProductObserver.php  # Cache invalidation on changes
│   │
│   └── Services/
│       ├── CatalogCache.php        # Versioned cache management
│       ├── CategoryResolver.php    # URL to database slug mapping
│       └── ThumbnailService.php    # Image processing
│
├── config/
│   ├── categories.php      # Catalog taxonomy definition
│   ├── cache.php          # Cache driver configuration
│   └── security.php       # Security policies
│
├── database/
│   └── migrations/        # Schema definitions
│
├── resources/
│   ├── views/
│   │   ├── catalog/       # Listing and detail pages
│   │   ├── components/    # Reusable Blade components
│   │   │   └── product/   # Product-specific components
│   │   ├── admin/         # Admin panel views
│   │   └── layouts/       # Page layouts
│   ├── js/               # Alpine.js components
│   └── css/              # Tailwind styles
│
├── storage/app/public/
│   ├── imports/          # Source product images
│   └── thumbnails/       # Generated WebP thumbnails
│
└── tests/
    ├── Unit/             # Service unit tests
    └── Feature/          # HTTP feature tests
```

---

## Data Models

### Product

The core entity representing a luxury item in the catalog.

```php
class Product extends Model
{
    protected $fillable = [
        'name',           // Display name (e.g., "Neverfull MM")
        'slug',           // URL slug (e.g., "neverfull-mm")
        'category_slug',  // Category reference (e.g., "louis-vuitton-women-bags")
        'gender',         // "women" or "men"
        'brand',          // Brand name (e.g., "Louis Vuitton")
        'section',        // Product type (e.g., "bags", "shoes")
        'folder',         // Image folder name
        'image',          // Primary image filename
        'image_path',     // Full path to primary image
    ];
}
```

**Database Indexes:**
- `category_slug` - Filtered listings
- `(category_slug, slug)` - Unique product lookup
- `brand` - Brand filtering
- `gender` - Gender filtering

### User

Admin users with role-based access control.

```php
class User extends Authenticatable
{
    // is_admin is NOT fillable (prevents privilege escalation)
    protected $fillable = ['name', 'email', 'password'];

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
}
```

---

## Services

### CatalogCache

Manages versioned caching for instant invalidation without clearing all cache data.

```php
class CatalogCache
{
    private const VERSION_KEY = 'catalog:version';
    private const TTL = 1800; // 30 minutes

    // Get current version number
    public function getVersion(): int;

    // Build versioned cache key
    public function key(string $slugsHash, int $page): string;
    // Returns: "catalog:v{version}:{hash}:p{page}"

    // Cache with automatic versioning
    public function remember(string $slugsHash, int $page, Closure $callback): array;

    // Invalidate all cached data by bumping version
    public function invalidate(): void;
}
```

**How Versioned Caching Works:**

1. Each cache key includes a version number: `catalog:v5:abc123:p1`
2. When products change, version bumps from 5 → 6
3. Old keys (`v5`) are never read again (orphaned)
4. Old entries expire naturally via TTL
5. No need to scan/delete old keys

### ThumbnailService

Generates optimized WebP thumbnails on-demand with thundering herd protection.

```php
class ThumbnailService
{
    private const SIZES = [
        'card'    => ['width' => 400, 'height' => 300, 'quality' => 80],
        'gallery' => ['width' => 800, 'height' => 800, 'quality' => 85],
        'thumb'   => ['width' => 96,  'height' => 96,  'quality' => 75],
    ];

    // Get URL, generating if needed
    public function getUrl(string $path, string $size): ?string;

    // Generate single thumbnail
    public function generate(string $path, string $size): ?string;

    // Generate all sizes (optimized - loads image once)
    public function generateAll(string $path): array;
}
```

**Thundering Herd Protection:**

```php
// Uses cache lock to prevent multiple processes generating same thumbnail
$lock = Cache::lock("thumb:{$path}:{$size}", 30);

if ($lock->get()) {
    try {
        // Generate thumbnail
    } finally {
        $lock->release();
    }
}
```

### CategoryResolver

Maps URL slugs to database queries for hierarchical category navigation.

```php
class CategoryResolver
{
    // Resolve URL slug to database category_slugs
    public function resolve(string $slug): array;
    // Returns: ['slugs' => [...], 'active' => [...]]

    // Get category taxonomy (cached 24h)
    public function getCategoryMap(): array;

    // Generate deterministic hash for cache keys
    public function hashSlugs(array $slugs): string;
}
```

**Resolution Logic:**

| URL Slug | Type | Database Query |
|----------|------|----------------|
| `women` | Gender | All categories where `gender = 'women'` |
| `women-bags` | Section | All categories in women + bags |
| `louis-vuitton-women-bags` | Leaf | Exact match on `category_slug` |

---

## Caching Strategy

### Cache Layers

```
┌─────────────────────────────────────────────────┐
│              Application Cache                   │
├─────────────────────────────────────────────────┤
│  Versioned Catalog Cache     │  30 min TTL      │
│  - Product IDs per page      │  Instant invalidate│
├─────────────────────────────────────────────────┤
│  Category Map Cache          │  24 hour TTL     │
│  - Taxonomy from config      │  Rarely changes  │
├─────────────────────────────────────────────────┤
│  Thumbnail URL Cache         │  1 hour TTL      │
│  - Resolved thumbnail paths  │  Reduces I/O     │
├─────────────────────────────────────────────────┤
│  Product Images Cache        │  24 hour TTL     │
│  - File listings per product │  Avoids disk scan│
└─────────────────────────────────────────────────┘
```

### Cache Invalidation

```php
// ProductObserver.php
class ProductObserver
{
    public function saved(Product $product): void
    {
        app(CatalogCache::class)->invalidate();
    }

    public function deleted(Product $product): void
    {
        app(CatalogCache::class)->invalidate();
    }
}
```

### Recommended Drivers

| Environment | Driver | Configuration |
|-------------|--------|---------------|
| Development | `file` | `CACHE_STORE=file` |
| Production | `redis` | `CACHE_STORE=redis` |
| Single Server | `file` or `redis` | Either works |
| Multi-Server | `redis` | Required for consistency |

---

## Image Pipeline

### Storage Structure

```
storage/app/public/
├── imports/                          # Source images
│   ├── lv-bags-women/
│   │   ├── neverfull-mm/
│   │   │   ├── 0000.jpg            # Primary image
│   │   │   ├── 0001.jpg            # Additional angles
│   │   │   └── 0002.jpg
│   │   └── speedy-25/
│   │       └── 0000.jpg
│   ├── chanel-shoes-women/
│   └── dior-clothes-men/
│
└── thumbnails/                       # Generated WebP
    ├── card/                         # 400×300
    │   └── imports/lv-bags-women/neverfull-mm/0000.webp
    ├── gallery/                      # 800×800
    │   └── imports/lv-bags-women/neverfull-mm/0000.webp
    └── thumb/                        # 96×96
        └── imports/lv-bags-women/neverfull-mm/0000.webp
```

### Generation Flow

```
┌──────────────┐    ┌─────────────┐    ┌──────────────┐
│  HTTP Request │ →  │ Check Cache │ →  │ Return URL   │
│  for thumbnail│    │ for URL     │    │ if cached    │
└──────────────┘    └─────────────┘    └──────────────┘
                           │
                           ▼ (cache miss)
                    ┌─────────────┐
                    │ Check if    │
                    │ file exists │
                    └─────────────┘
                           │
                           ▼ (not exists)
                    ┌─────────────┐
                    │ Acquire     │
                    │ cache lock  │
                    └─────────────┘
                           │
                           ▼
                    ┌─────────────┐
                    │ Generate    │
                    │ WebP thumb  │
                    └─────────────┘
                           │
                           ▼
                    ┌─────────────┐
                    │ Cache URL   │
                    │ & return    │
                    └─────────────┘
```

### Folder Name Resolution

The `resolveStorageFolder()` method dynamically maps category slugs to storage folders:

```php
// Category slug format: {brand}-{gender}-{section}
// Storage folder format: {brand-prefix}-{section}-{gender}

// Examples:
'louis-vuitton-women-bags' → 'lv-bags-women'
'chanel-men-shoes'         → 'chanel-shoes-men'
'hermes-women-belts'       → 'hermes-belts-women'
```

---

## Category System

### Taxonomy Definition

Categories are defined in `config/categories.php`:

```php
return [
    'women' => [
        'Bags' => [
            ['name' => 'All Women Bags', 'href' => '/catalog/women-bags'],
            ['name' => 'Louis Vuitton Bags', 'params' => ['category' => 'louis-vuitton-women-bags']],
            ['name' => 'Chanel Bags', 'params' => ['category' => 'chanel-women-bags']],
            // ...
        ],
        'Shoes' => [
            // ...
        ],
    ],
    'men' => [
        // ...
    ],
];
```

### Category Types

| Type | Example | Resolves To |
|------|---------|-------------|
| Gender | `women` | All leaf categories for women |
| Section | `women-bags` | All leaf categories in women + bags |
| Leaf | `louis-vuitton-women-bags` | Direct database match |

### URL Routing

```
/catalog                           → All products
/catalog/women                     → All women's products
/catalog/women-bags                → All women's bags
/catalog/louis-vuitton-women-bags  → Louis Vuitton women's bags
/catalog/louis-vuitton-women-bags/neverfull-mm  → Product detail
```

---

## Request Flow

### Catalog Page Request

```
1. Request: GET /catalog/women-bags?page=2

2. CategoryController::show('women-bags')
   │
   ├─→ CategoryResolver::resolve('women-bags')
   │   └─→ Returns: ['slugs' => ['lv-women-bags', 'chanel-women-bags', ...]]
   │
   ├─→ CatalogCache::remember(hash, page, callback)
   │   ├─→ Check: catalog:v5:abc123:p2 exists?
   │   │   └─→ Yes: Return cached data
   │   │   └─→ No: Execute callback
   │   │       ├─→ Query: SELECT id FROM products WHERE category_slug IN (...)
   │   │       └─→ Cache result with version key
   │
   ├─→ Hydrate products from IDs
   │   └─→ SELECT * FROM products WHERE id IN (...)
   │
   └─→ Return view with paginated products

3. View renders product cards
   │
   └─→ Each card calls ThumbnailService::getUrl()
       └─→ Returns WebP thumbnail URL (generating if needed)
```

### Product Detail Request

```
1. Request: GET /catalog/louis-vuitton-women-bags/neverfull-mm

2. ProductController::show('louis-vuitton-women-bags', 'neverfull-mm')
   │
   ├─→ Find product by category_slug + slug
   │
   ├─→ resolveStorageFolder(category_slug)
   │   └─→ 'louis-vuitton-women-bags' → 'lv-bags-women'
   │
   ├─→ Build image gallery
   │   ├─→ Cache::remember("product.images.{id}")
   │   │   └─→ Scan folder for image files
   │   └─→ Map each image to {src, thumb, original}
   │
   ├─→ Get related products (versioned cache)
   │
   └─→ Return view with product, images, related
```

---

## Security

### Authentication

- **Laravel Breeze**: Standard email/password authentication
- **Password Hashing**: Bcrypt with 12 rounds
- **Session Security**: Database-backed sessions with CSRF protection

### Authorization

```php
// AdminMiddleware.php
public function handle($request, Closure $next)
{
    if (!auth()->check() || !auth()->user()->isAdmin()) {
        abort(403);
    }
    return $next($request);
}
```

### Mass Assignment Protection

```php
// User model - is_admin is NOT fillable
protected $fillable = ['name', 'email', 'password'];

// Prevents: User::create(['is_admin' => true])
```

### Security Headers

```php
// SecurityHeaders middleware adds:
// - X-Content-Type-Options: nosniff
// - X-Frame-Options: DENY
// - X-XSS-Protection: 1; mode=block
```

### File Upload Security

```php
// config/security.php
'uploads' => [
    'max_size' => 10 * 1024 * 1024, // 10MB
    'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp'],
    'blocked_extensions' => ['php', 'exe', 'bat', 'sh', 'phtml'],
];
```

---

## Testing

### Test Structure

```
tests/
├── Unit/
│   ├── CatalogCacheTest.php       # Versioned caching logic
│   ├── CategoryResolverTest.php   # Slug resolution
│   ├── ThumbnailServiceTest.php   # Image processing
│   └── PerformanceTest.php        # Performance benchmarks
│
└── Feature/
    ├── Auth/                      # Authentication flows
    └── ProfileTest.php            # User profile CRUD
```

### Running Tests

```bash
# All tests
php artisan test

# Specific suite
php artisan test tests/Unit/

# Specific test
php artisan test --filter=CatalogCacheTest

# With coverage
php artisan test --coverage
```

### Test Configuration

```xml
<!-- phpunit.xml -->
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="CACHE_STORE" value="array"/>
<env name="SESSION_DRIVER" value="array"/>
```

---

## Performance Considerations

### Database Optimization

- **Indexes** on `category_slug`, `brand`, `gender`
- **Lightweight caching**: Only IDs are cached, not full models
- **Deterministic pagination**: Uses `forPage()` not `paginate()` for cache consistency

### Image Optimization

- **WebP format**: 25-35% smaller than JPEG at same quality
- **On-demand generation**: Only generates what's requested
- **Lock protection**: Prevents duplicate generation under load

### Cache Efficiency

- **Versioned keys**: No expensive cache clearing operations
- **Appropriate TTLs**: Balance freshness vs. hit rate
- **ID-only caching**: Minimizes serialization overhead

### Frontend Performance

- **Pre-built assets**: No build step required in production
- **Tailwind purging**: Only used CSS classes in production
- **Alpine.js**: Minimal JS footprint (~15KB gzipped)
- **Infinite scroll**: Loads content progressively

---

## Deployment Checklist

```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies (production)
composer install --no-dev --optimize-autoloader

# 3. Run migrations
php artisan migrate --force

# 4. Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 6. Verify environment
php artisan env
# Should show: production
```

### Production Environment Variables

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

CACHE_STORE=redis
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true

LOG_CHANNEL=daily
LOG_LEVEL=warning
```
