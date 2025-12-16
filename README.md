# HeirLuxury

A luxury product e-commerce catalog built with Laravel 12, featuring multi-brand support, optimized image handling, and hierarchical category navigation.

## Features

- **Multi-Brand Catalog**: Louis Vuitton, Chanel, Dior, Hermès, Gucci, Celine, Prada, YSL
- **Hierarchical Categories**: Gender → Section → Brand organization
- **Optimized Images**: On-demand WebP thumbnail generation in multiple sizes
- **Infinite Scroll**: AJAX-powered pagination with Alpine.js
- **Versioned Caching**: Instant cache invalidation on product changes
- **Admin Panel**: Full CRUD for products and categories
- **Responsive Design**: Tailwind CSS with mobile-first approach

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12, PHP 8.2+ |
| Database | SQLite (dev), MySQL/PostgreSQL (prod) |
| Frontend | Vite 5, Tailwind CSS 3, Alpine.js 3 |
| Images | Intervention Image (WebP thumbnails) |
| Auth | Laravel Breeze |
| Caching | File (dev), Redis (prod) |

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- SQLite / MySQL / PostgreSQL

## Installation

```bash
# Clone the repository
git clone https://github.com/simonkowalski1/HeirLuxury.git
cd HeirLuxury

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate

# Create storage symlink
php artisan storage:link

# Build assets (pre-built assets are committed, but you can rebuild)
npm run build
```

## Configuration

### Environment Variables

```env
# Application
APP_NAME=HeirLuxury
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# Cache (use 'file' for dev, 'redis' for production)
CACHE_STORE=file

# Session
SESSION_DRIVER=database
```

### Cache Drivers

| Environment | Recommended | Why |
|-------------|-------------|-----|
| Development | `file` | Simple, debuggable, survives restarts |
| Production | `redis` | Shared across processes/servers, fast, atomic |

## Usage

### Importing Products

Products are imported from folder structures in `storage/app/public/imports/`:

```
imports/
├── lv-bags-women/
│   ├── neverfull-mm/
│   │   ├── 0000.jpg
│   │   ├── 0001.jpg
│   │   └── 0002.jpg
│   └── speedy-25/
│       └── 0000.jpg
├── chanel-shoes-women/
└── dior-clothes-men/
```

**Folder naming pattern**: `{brand-prefix}-{section}-{gender}`

| Brand | Prefix |
|-------|--------|
| Louis Vuitton | `lv` |
| Chanel | `chanel` |
| Dior | `dior` |
| Hermès | `hermes` |

```bash
# Import products
php artisan import:lv

# Fresh import (clears existing products)
php artisan import:lv --fresh

# Skip thumbnail generation during import
php artisan import:lv --skip-thumbnails
```

### Generating Thumbnails

Thumbnails are generated on-demand, but you can batch generate:

```bash
# Generate all thumbnails
php artisan thumbnails:generate

# Specific folder only
php artisan thumbnails:generate --folder=lv-bags-women

# Specific size only (card, gallery, thumb)
php artisan thumbnails:generate --size=card

# Force regenerate existing
php artisan thumbnails:generate --force
```

### Thumbnail Sizes

| Size | Dimensions | Quality | Use Case |
|------|------------|---------|----------|
| card | 400×300 | 80% | Catalog grid |
| gallery | 800×800 | 85% | Product detail |
| thumb | 96×96 | 75% | Gallery navigation |

## Development

```bash
# Start Vite dev server (hot reload)
npm run dev

# Run tests
php artisan test

# Run specific test suite
php artisan test tests/Unit/
php artisan test tests/Feature/
```

## Production Deployment

### Option 1: Build on Server

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Option 2: Pre-built Assets (Recommended)

Assets are pre-built and committed to the repository. No Node.js required on server:

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Production Environment

```env
APP_ENV=production
APP_DEBUG=false
CACHE_STORE=redis
SESSION_SECURE_COOKIE=true
```

## Admin Access

1. Register a user account
2. Manually set `is_admin=1` in the database:
   ```sql
   UPDATE users SET is_admin = 1 WHERE email = 'admin@example.com';
   ```
3. Access admin panel at `/admin`

## Project Structure

```
app/
├── Console/Commands/     # Artisan commands (import, thumbnails)
├── Http/Controllers/     # Web controllers
│   └── Admin/           # Admin panel controllers
├── Models/              # Eloquent models
├── Services/            # Business logic
│   ├── CatalogCache.php      # Versioned caching
│   ├── CategoryResolver.php  # URL slug resolution
│   └── ThumbnailService.php  # Image optimization
└── Observers/           # Model observers

config/
├── categories.php       # Catalog taxonomy
├── cache.php           # Cache configuration
└── security.php        # Security settings

storage/app/public/
├── imports/            # Product image folders
└── thumbnails/         # Generated WebP thumbnails
```

## Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter=CatalogCacheTest
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/catalog/products` | Paginated products (infinite scroll) |

Query parameters:
- `page` - Page number (default: 1)
- `category` - Category slug filter

## Architecture

See [ARCHITECTURE.md](ARCHITECTURE.md) for detailed technical documentation.

## License

Proprietary - All rights reserved.

## Support

For issues and feature requests, please use the [GitHub Issues](https://github.com/simonkowalski1/HeirLuxury/issues) page.
