# HeirLuxury — Prompt Plan: Quality Infrastructure Setup

This plan implements the quality engineering practices defined in `spec.md`.
Each prompt is a self-contained step. Execute them in order. Mark each as
✅ when complete.

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

## Notes

- The spec for this plan is in `.claude/spec.md`
- All prompts reference the spec for exact configurations and requirements
- Each prompt should be committed separately with a clear message
- Pause after each prompt for review before proceeding to the next
