# HeirLuxury — Prompt Plan

This plan tracks all implementation work for HeirLuxury.
Each prompt is a self-contained step. Execute them in order. Mark each as
✅ when complete.

---

## Phase 1: Quality Infrastructure Setup

Implements the quality engineering practices defined in `spec.md`.

---

## Prompt 1: Install and Configure ESLint + Prettier

**Status**: ✅ Complete

```text
Install ESLint 9+ and Prettier as dev dependencies. Configure them for
the HeirLuxury project which uses Alpine.js, Vite 5, and Tailwind CSS.

1. Run: npm install --save-dev eslint prettier eslint-config-prettier @eslint/js
2. Create eslint.config.js using flat config format (ESLint 9+):
   - Include @eslint/js recommended rules
   - Include eslint-config-prettier to disable formatting rules
   - Scope to resources/js/**/*.js
   - Add Alpine, axios, window, document, console, fetch as readonly globals
3. Create .prettierrc with: semi=true, singleQuote=false, tabWidth=4,
   trailingComma=all, printWidth=100
4. Create .prettierignore to exclude: node_modules, vendor, public/build,
   storage, bootstrap/cache
5. Run eslint and prettier against existing files to verify config works
6. Fix any issues found in existing JS files

Refer to spec.md "Linting & Formatting" section for exact config.
```

---

## Prompt 2: Install and Configure pre-commit Hooks

**Status**: ✅ Complete

```text
Set up pre-commit hooks using the pre-commit Python package to enforce
quality gates on every commit.

1. Install pre-commit: uv tool install pre-commit
2. Create .pre-commit-config.yaml in the project root with these hooks:
   a. laravel-pint: runs ./vendor/bin/pint --test on *.php files
   b. eslint: runs npx eslint on *.js and *.mjs files
   c. prettier: runs npx prettier --check on *.js, *.css, *.json files
   d. phpunit: runs php artisan test on *.php files (pre-commit stage)
   All hooks use repo: local and language: system.
3. Run: pre-commit install
4. Verify by running: pre-commit run --all-files
5. Fix any failures found during the initial run
6. Do NOT use --no-verify at any point

Refer to spec.md "Pre-Commit Hooks" section for the exact YAML config.
```

---

## Prompt 3: Run Pint Against Existing Codebase

**Status**: ✅ Complete

```text
Run Laravel Pint across the entire codebase to establish a clean baseline.

1. Run: ./vendor/bin/pint --test to see current violations
2. Run: ./vendor/bin/pint to auto-fix all violations
3. Review the changes to make sure nothing was broken
4. Run: php artisan test to verify all tests still pass
5. Commit the formatting fixes with message:
   "Apply Laravel Pint formatting to establish clean baseline"

This is a one-time cleanup. After this, the pre-commit hook keeps it clean.
```

---

## Prompt 4: Audit Existing Tests and Identify Gaps

**Status**: ✅ Complete

```text
Review all existing tests and identify missing coverage. The project has:
- tests/Unit/: CatalogCacheTest, CategoryResolverTest, PerformanceTest,
  ThumbnailServiceTest
- tests/Feature/: Auth tests (Breeze), Admin/DashboardTest,
  Admin/ProductControllerTest, ProfileTest

For each model and controller in app/, check whether tests exist for:
- Unit tests: model relationships, scopes, accessors, mutators, business rules
- Feature tests: every route/controller action (index, show, create, store,
  edit, update, destroy)
- Edge cases from spec.md: empty states, boundary values, auth boundaries,
  cache invalidation, image processing, locale switching

Output a list of missing tests as specific, actionable items.
Do NOT write the tests yet — just catalog what's missing.
```

---

## Prompt 5: Write Missing Unit Tests (TDD Red Phase)

**Status**: ✅ Complete

```text
Using the gap list from Prompt 4, write failing unit tests for the
highest-priority gaps. Follow TDD:

1. Pick the first missing unit test from the gap list
2. Write the test file in tests/Unit/
3. Run: php artisan test --filter=<TestName> to confirm it fails (red)
4. Document what the test expects

Write tests for these areas first (in priority order):
- Product model: price validation, slug uniqueness, brand relationship
- Category model: hierarchical parent/child, breadcrumb generation
- Brand model: name uniqueness, product relationship
- Image model: cleanup on delete, WebP conversion validation

Do NOT write implementation code. Only write tests.
Mark each test with a clear assertion message so failures are descriptive.
```

---

## Prompt 6: Make Unit Tests Pass (TDD Green Phase)

**Status**: ✅ Complete

```text
Take each failing test from Prompt 5 and write the minimal implementation
code to make it pass. Follow TDD green phase strictly:

1. Pick the first failing test
2. Write the minimum code to make ONLY that test pass
3. Run: php artisan test --filter=<TestName> to confirm green
4. Move to the next failing test
5. After all tests pass, run the full suite: php artisan test
6. Commit with a descriptive message

Do NOT refactor yet. Do NOT add extra features. Just make the tests green.
```

---

## Prompt 7: Write Missing Feature Tests (TDD Red Phase)

**Status**: ✅ Complete

```text
Using the gap list from Prompt 4, write failing feature tests for
missing controller/route coverage. Follow TDD:

1. Pick the first missing feature test from the gap list
2. Write the test in tests/Feature/ using appropriate subdirectory
3. Use RefreshDatabase trait and factory-generated test data
4. Run: php artisan test --filter=<TestName> to confirm it fails (red)

Focus on these areas first:
- Catalog browsing: product index, product show, category filtering
- Brand pages: brand listing, brand products
- Search: product search, empty results
- Admin CRUD: full lifecycle for products (if not already covered)
- Auth boundaries: guest vs authenticated vs admin for each route

Do NOT write implementation code. Only write tests.
```

---

## Prompt 8: Make Feature Tests Pass (TDD Green Phase)

**Status**: ✅ Complete

```text
Take each failing feature test from Prompt 7 and write the minimal
implementation code to make it pass. Follow TDD green phase:

1. Pick the first failing test
2. Write the minimum code (routes, controller methods, views) to pass
3. Run: php artisan test --filter=<TestName> to confirm green
4. Move to the next failing test
5. After all pass, run full suite: php artisan test
6. Run pre-commit hooks: pre-commit run --all-files
7. Commit only after all hooks pass
```

---

## Prompt 9: Refactor with Green Tests (TDD Refactor Phase)

**Status**: ✅ Complete

```text
With all tests passing, refactor the codebase for clarity and
maintainability. The test suite is your safety net.

1. Review code for duplication, unclear naming, or overly complex logic
2. Make small refactoring changes
3. After EACH change, run: php artisan test to verify nothing broke
4. Run: pre-commit run --all-files after each batch of changes
5. Commit frequently with clear messages

Rules:
- Only refactor code that is covered by tests
- Do not add new features during refactoring
- Do not change behavior — only improve structure
- Keep changes small and reversible
```

---

## Prompt 10: Final Verification and Cleanup

**Status**: ✅ Complete

```text
Final quality verification pass:

1. Run full test suite: php artisan test
2. Run all linters:
   - ./vendor/bin/pint --test
   - npx eslint resources/js/
   - npx prettier --check "resources/**/*.{js,css,json}"
3. Run pre-commit on all files: pre-commit run --all-files
4. Verify no --no-verify or hook bypass was used in git log
5. Review git log for clean, descriptive commit messages
6. Confirm spec.md and promt_plan.md are up to date
7. Mark all prompts as ✅ complete

If anything fails, fix it before marking as done.
```

---

---

## Phase 2: Feature Work & Cleanup

---

## Prompt 11: Add Wishlist / Favorites Feature

**Status**: ✅ Complete

```text
Add a wishlist (favorites) feature that lets users save products to a
persistent list, accessible from the navbar.

1. Backend:
   - Create a `wishlist_items` table (migration) with columns:
     session_id (for guests), user_id (nullable, for logged-in users),
     product_id, created_at
   - Create WishlistController with: toggle (add/remove), index (list), count
   - Add API routes: POST /wishlist/toggle/{product}, GET /wishlist,
     GET /wishlist/count
   - Use session-based storage for guests, DB for authenticated users
   - Merge session wishlist into user wishlist on login

2. Frontend — Heart Button:
   - Add a heart icon button (outline when not saved, filled when saved)
     on product cards (resources/views/components/product/card.blade.php)
     and on the product detail page
   - Use Alpine.js to toggle wishlist state via fetch() to the API
   - Animate the heart on toggle (scale + color transition)

3. Frontend — Navbar Wishlist Icon:
   - Add a heart icon to the navbar (resources/views/layouts/navbar.blade.php)
     to the LEFT of the contact button in the right-side group
   - Show a small badge/counter with the number of wishlist items
   - On click, open a slide-out panel or navigate to a wishlist page
     showing saved products with remove buttons
   - Include on both desktop and mobile navbar sections

4. Wishlist Page/Panel:
   - Create a wishlist view showing saved products as cards
   - Each card has a remove (heart toggle) button
   - Empty state with a message like "Your wishlist is empty"
   - Link to product detail page from each card

5. Tests (TDD):
   - Write tests FIRST for: toggle add, toggle remove, count endpoint,
     guest session persistence, authenticated user persistence,
     session-to-user merge on login
   - Then implement to make tests pass

6. Run pre-commit hooks and commit when all tests pass.
```

---

## Prompt 12: Update Sidenav and Mega Menu from brands.tsv

**Status**: ⬜ Incomplete

```text
Update the catalog sidenav and mega menu to reflect the current brand
catalog as defined in brands.tsv.

Source file: C:\Users\simon\Documents\Scripts\Scraper\brands.tsv
Format: brand<TAB>subcat<TAB>url (lines starting with # are comments)

Brands in brands.tsv (the source of truth):
  Nike, Yeezy, Versace, Moncler, Philipp Plein, OFFwhite, McQueen,
  Amiri, Gucci, Celine, Givenchy, Dior, Chanel, LV

Brands currently in config/categories.php but NOT in brands.tsv:
  Hermès — REMOVE from config (no longer sourced)

Brands in brands.tsv but NOT yet in config/categories.php:
  Amiri, Gucci, Philipp Plein — ADD to config

New sections/categories to add:
  Women: Gucci Bags, Gucci Shoes, Gucci Clothes, Gucci Jewelry,
         Gucci Glasses, Amiri Women Clothes, Amiri Women Shoes
  Men:   Gucci Men Shoes, Gucci Men Clothes, Gucci Men Belts,
         Amiri Men Clothes, Amiri Men Shoes, Philipp Plein Men Shoes,
         Givenchy Men Glasses

Sections to remove (brand no longer sourced):
  All Hermès entries (bags, shoes, clothing, belts, jewelry, glasses)

1. Parse brands.tsv and compare against config/categories.php:
   - The subcat column format is "{Brand} {Gender} {section}"
   - Map section names: bags->Bags, shoes->Shoes, clothes->Clothing,
     belts->Belts, jewelry->Jewelry, glasses->Glasses
   - Slug format: {brand-lowercase}-{gender}-{section}
     e.g. "Gucci Women bags" -> slug "gucci-women-bags"

2. Update config/categories.php:
   - Remove all Hermès entries
   - Add Gucci entries (Women: bags, shoes, clothes, jewelry, glasses;
     Men: shoes, clothes, belts)
   - Add Amiri entries (Women: clothes, shoes; Men: clothes, shoes)
   - Add Philipp Plein (Men: shoes)
   - Add Givenchy Men Glasses, Givenchy Women Jewelry
   - Add Chanel Women Belts (present in TSV, missing from config)
   - Verify Versace Men Belts is present (it is in config)
   - Keep existing display name conventions (e.g. "Louis Vuitton" not "LV")
   - Sort brands alphabetically within each section

3. Verify the mega menu (resources/views/catalog/mega.blade.php)
   renders correctly with the updated config:
   - Check that brand links resolve to valid routes
   - Ensure gender tabs (Women/Men) show correct sections
   - Verify section groupings match the updated config

4. Verify the sidenav (resources/views/catalog/_sidenav.blade.php)
   also reflects the updated categories correctly

5. Test that all category routes work by running existing feature tests
6. Run pre-commit hooks and commit when passing
```

---

## Prompt 13: Review and Purge Artisan Commands

**Status**: ⬜ Incomplete

```text
Audit all custom Artisan commands in app/Console/Commands/ and remove
any that are no longer needed.

Current commands to review:
- BackfillSlugs.php — Was this a one-time migration? Is it still needed?
- BackfillProductSlugs.php — Same question. Overlaps with BackfillSlugs?
- DeduplicateImages.php — One-time cleanup or ongoing utility?
- GenerateThumbnails.php — Likely still needed for image processing
- ImportBrands.php — Still needed if brands.tsv import is active
- ImportLV.php — One-time LV product import? Still relevant?

For each command:
1. Read the source code and understand what it does
2. Check git log for when it was last modified and used
3. Determine if it's: KEEP (ongoing utility), ARCHIVE (document and
   remove), or DELETE (no longer relevant)
4. For commands marked DELETE/ARCHIVE:
   - Verify no scheduled tasks or other code references them
   - Remove the file
5. For commands marked KEEP:
   - Verify they still work by running with --dry-run or --help
   - Check for hardcoded paths or outdated assumptions
   - Fix any issues found

6. Also check for one-off utility scripts in the project root:
   - check_folders.php — One-off script with hardcoded local paths
   - copy_products_to_imports.php — One-off migration script
   - reorganize-imports.ps1 — PowerShell utility script
   Delete these if they are no longer needed.

7. Run tests and pre-commit hooks, then commit.
```

---

## Notes

- The spec for this plan is in `.claude/spec.md`
- All prompts reference the spec for exact configurations and requirements
- Each prompt should be committed separately with a clear message
- Pause after each prompt for review before proceeding to the next
