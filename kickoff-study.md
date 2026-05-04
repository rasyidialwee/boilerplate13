# Kickoff — Reference study for improving your Laravel boilerplate

This document is a **structured study** of [Kickoff](https://github.com/cleaniquecoders/kickoff) (package `cleaniquecoders/kickoff`) and its public site [kickoff.my](https://kickoff.my/), with emphasis on **developer tooling** (Pint, PHPStan/Larastan, Rector, CI/CD, testing). Use it to compare patterns against a mature boilerplate.

---

## Table of contents

1. [Project overview](#1-project-overview)
2. [Full stack](#2-full-stack)
3. [Package suite (Spatie & ecosystem)](#3-package-suite-spatie--ecosystem)
   - [Laravel Media Secure](#laravel-media-secure-cleaniquecoderslaravel-media-secure)
   - [Traitify](#traitify-cleaniquecoderstraitify)
4. [Application architecture](#4-application-architecture)
5. [Deep dive: dev tools config](#5-deep-dive-dev-tools-config)
6. [CI/CD workflows](#6-cicd-workflows)
7. [Automation scripts (`stubs/bin/`)](#7-automation-scripts-stubsbin)
8. [Infrastructure templates](#8-infrastructure-templates)
9. [Testing setup](#9-testing-setup)
10. [What to adopt — key takeaways](#10-what-to-adopt--key-takeaways)
11. [Gaps and repo realities](#11-gaps-and-repo-realities)

---

## 1. Project overview

### What Kickoff is

Kickoff is **not** a Laravel application package you `composer require` into an app. It is a **Composer global CLI tool**:

- **Package name:** `cleaniquecoders/kickoff`
- **Binary:** `bin/kickoff` (Symfony Console application with a `start` command)
- **Mechanism:** Copies everything under `stubs/` into the target project path and runs post-install steps (Composer merge, `composer require`, npm, artisan tasks).

This matches the marketing on [kickoff.my](https://kickoff.my/): install globally, then scaffold in seconds.

### How `kickoff start` works

```text
kickoff start <owner> <project-name> [path]
```

| Argument | Role |
|----------|------|
| `owner` | Replaces `${OWNER}` in README, `.env.example`, etc. |
| `name` | Project name; drives DB name (snake_case) and `${PROJECT_NAME}` placeholders |
| `path` | Optional. Default: `./<project-name>`. Use `.` or a path for an **existing** Laravel app |

**Flow (from `StartCommand.php`):**

1. If the path has no valid Laravel project (`artisan` missing), run **`laravel new`** with fixed flags (see below).
2. **Copy** `stubs/` recursively into the project (overwriting files).
3. **Patch `composer.json`:** autoload `support/helpers.php`, Pest plugin allow-plugin, and **Composer scripts** for QA.
4. Replace placeholders (`${PROJECT_NAME}`, `${OWNER}`) in README, `.env.example`, `bin/*`.
5. **`composer require`** a curated list of packages + dev tools.
6. **`php artisan vendor:publish`** for many tags.
7. **`php artisan boost:install`** (Laravel Boost — guidelines, skills, MCP).
8. **`npm install lodash axios tippy.js`** (unless `--skip-npm`).
9. **Run** `bin/install`, `npm run build`, `key:generate`, notifications table, `reload:db`.
10. Restore `.gitignore` files from stubs, final git commit.

**CLI options:**

- `--dry-run` — print planned steps without changes
- `--skip-packages` — skip Composer/npm installs and publishes (after stub copy)
- `--skip-npm` — skip only extra npm packages

### Target Laravel scaffold

New projects are created with:

```bash
laravel new <path> --git --livewire --pest --npm --livewire-class-components --no-interaction
```

That implies: **Git**, **Livewire**, **Pest**, **NPM/Vite**, **Livewire class-based components** — aligned with Laravel’s current “starter” story and the [kickoff.my](https://kickoff.my/) copy.

### Kickoff as a project website (`kickoff.my`)

The separate marketing site is worth copying as a **pattern**: a dedicated landing page describing features, screenshots, FAQ, install commands, and links to GitHub/Packagist — independent of the Composer package README. For your boilerplate, consider the same split: **product story + docs** on the web, **technical README** in-repo.

---

## 2. Full stack

These are the **intended** layers for a generated app (from stubs + install steps + site claims).

| Layer | Choice |
|-------|--------|
| **PHP** | 8.4+ in tests/arch rules; Rector targets **PHP 8.5**; package requires PHP ^8.3 |
| **Framework** | Laravel 12 (site and ecosystem alignment: Livewire 4, Pest, etc.) |
| **Auth** | Laravel **Fortify** (settings routes use Fortify feature checks in stubs) |
| **UI** | **Livewire 4** + **Flux** (`livewire/flux`), **Blade**, **Blade Icons** + Lucide (`mallardduck/blade-lucide-icons`) |
| **API auth** | Laravel **Sanctum** (`auth:sanctum` on several route groups) |
| **Queues** | **Horizon** + **Redis** (`predis/predis`) |
| **Debugging** | **Telescope**, **Debugbar** (dev) |
| **Breadcrumbs** | `diglactic/laravel-breadcrumbs` |
| **Impersonation** | `lab404/laravel-impersonate` |
| **Search (local dev)** | **Meilisearch** in `docker-compose.yml` |
| **Object storage** | **MinIO** in docker-compose (S3-compatible) |
| **Mail (local)** | **Mailpit** in docker-compose |

### Composer “dev” script in generated apps

`StartCommand` injects a `dev` script using **`concurrently`** to run server, queue listener, **Pail**, and **Vite** — a single command local DX pattern.

---

## 3. Package suite (Spatie & ecosystem)

Installed by `installPackages()` in `StartCommand.php` (not exhaustive commentary — see source for exact versions at install time).

### Production (`require`)

| Package | Purpose |
|---------|---------|
| `laravel/sanctum` | SPA/mobile API tokens |
| `blade-ui-kit/blade-icons` | Icon components |
| `cleaniquecoders/laravel-media-secure` | Secure media URLs / access |
| `cleaniquecoders/traitify` | Shared traits pattern |
| `diglactic/laravel-breadcrumbs` | Breadcrumbs |
| `lab404/laravel-impersonate` | User impersonation |
| `laravel/horizon` | Queue dashboard |
| `laravel/telescope` | Debugging / requests (gated in routes) |
| `livewire/livewire` ^4 | Livewire 4 |
| `livewire/flux` | Flux UI |
| `mallardduck/blade-lucide-icons` | Lucide icon set |
| `owen-it/laravel-auditing` | Model auditing |
| `predis/predis` | Redis client |
| `spatie/laravel-activitylog` | Activity log |
| `spatie/laravel-medialibrary` | Media attachments |
| `cleaniquecoders/media-manager` | Media manager UI |
| `spatie/laravel-permission` | Roles & permissions |
| `spatie/laravel-settings` | Typed settings |
| `yadahan/laravel-authentication-log` | Login attempt logging |

### Development (`require-dev`)

| Package | Purpose |
|---------|---------|
| `barryvdh/laravel-debugbar` | Debug bar |
| `cleaniquecoders/laravel-db-doc` | DB documentation tooling |
| `driftingly/rector-laravel` | Rector Laravel rules |
| `laravel/boost` | AI/docs integration (`boost:install`) |
| `larastan/larastan` | PHPStan for Laravel |
| `pestphp/pest-plugin-arch` | Architecture tests |

**Note:** The public site lists “Activity Logging” with Spatie Activity Log; the starter **also** ships **Laravel Auditing** (`owen-it/laravel-auditing`) — two complementary audit stories (activity stream vs model audit).

### Laravel Media Secure (`cleaniquecoders/laravel-media-secure`)

Source: [github.com/cleaniquecoders/laravel-media-secure](https://github.com/cleaniquecoders/laravel-media-secure).

This package sits **on top of [Spatie Laravel Media Library](https://spatie.be/docs/laravel-medialibrary)** and solves a common gap: **public disk URLs are not authorization-aware**. It exposes **authenticated, policy-backed** HTTP endpoints for media instead of leaking direct storage paths.

| Topic | What it provides |
|------|-------------------|
| **Routing** | UUID-based routes for **view**, **download**, and **stream** |
| **Authorization** | Policy-based checks with **delegation to the parent model** (e.g. document owner). When `strict = true`, you register policies for models that own media |
| **Signed URLs** | Time-limited share links (`get_signed_view_url`, etc.) — useful for email, embeds, or external clients without session auth |
| **HTTP behavior** | **ETag** / **Last-Modified** for caching; **streaming** for large files (memory-efficient) |
| **Middleware** | Configurable middleware stack |
| **DX** | Helpers such as `get_view_media_url($media)`, `get_download_media_url($media)`, `get_stream_media_url($media)`; facade `LaravelMediaSecure` for signed helpers |

**Install / publish in any Laravel app:**

```bash
composer require cleaniquecoders/laravel-media-secure
php artisan vendor:publish --tag="media-secure-config"
```

**Signed URL config (from package docs):** `config/laravel-media-secure.php` includes a `signed` block (`enabled`, `prefix`, `route_name`, `expiration` in minutes). Env examples: `LARAVEL_MEDIA_SECURE_SIGNED_ENABLED`, `LARAVEL_MEDIA_SECURE_SIGNED_EXPIRATION`.

**Why Kickoff includes it:** Together with `spatie/laravel-medialibrary` and `cleaniquecoders/media-manager`, you get **upload + browse UI + secure delivery** instead of only storage paths.

**Maintainer tooling:** The package repo itself uses **PHPStan** (`phpstan.neon.dist`, baseline), **Rector**, and tests — same engineering discipline as Kickoff’s dev stack.

---

### Traitify (`cleaniquecoders/traitify`)

Source: [github.com/cleaniquecoders/traitify](https://github.com/cleaniquecoders/traitify).

A **model-centric utility library**: reusable **traits**, **contracts**, and a **generator** system so you stop duplicating UUID, slug, token, and metadata patterns across models.

| Topic | What it provides |
|------|-------------------|
| **Traits** | Eleven built-ins (see table below) for UUID, tokens, slugs, JSON meta, user stamping, API shaping, search, etc. |
| **Configuration** | **Three-tier resolution:** per-model overrides → package config → defaults |
| **Generators** | Customizable UUID / token / slug generation (length, prefix, pool, uniqueness) |
| **DX** | Documented as “zero config” with sensible defaults; Pest-tested |

**Quick example (from upstream README):**

```php
use CleaniqueCoders\Traitify\Concerns\InteractsWithUuid;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use InteractsWithUuid;
}
```

**Trait inventory (from upstream docs):**

| Trait | Purpose |
|-------|---------|
| `InteractsWithUuid` | Auto UUID (optional UUID primary key via `$uuid_column`) |
| `InteractsWithToken` | Secure API-style tokens (length, prefix, pool) |
| `InteractsWithSlug` | SEO slugs with uniqueness / max length |
| `InteractsWithMeta` | JSON metadata |
| `InteractsWithUser` | User relationship stamping |
| `InteractsWithApi` | API response shaping |
| `InteractsWithSearchable` | Full-text search helpers |
| `InteractsWithDetails` | Eager-load / detail patterns |
| `InteractsWithEnum` | Enum helpers |
| `InteractsWithResourceRoute` | Resource route generation |
| `InteractsWithSqlViewMigration` | SQL view migrations |

**Why Kickoff includes it:** Generated apps use **consistent model conventions** (UUIDs, tokens, slugs) without each project reinventing helpers — aligns with a **boilerplate that encodes team standards**.

**Maintainer tooling:** Same pattern as Media Secure — **PHPStan**, **Rector**, **Pest** in the package repository.

---

## 4. Application architecture

### Route organization

- [`stubs/routes/web.php`](kickoff-main/kickoff-main/stubs/routes/web.php) loads every file matching `routes/web/*.php` via a helper:

```php
require_all_in(base_path('routes/web/*.php'));
```

- [`stubs/support/helpers.php`](kickoff-main/kickoff-main/stubs/support/helpers.php) defines `require_all_in()` and loads sibling helper files under `support/`.

**Benefit:** Add a new route file without touching a central switch statement.

### Route modules (examples from stubs)

| File | Concern |
|------|---------|
| `routes/web/_.php` | Home, dashboard |
| `routes/web/auth.php` | Fortify-related settings (Livewire pages) |
| `routes/web/administration.php` | `/admin` — roles, settings (policy middleware) |
| `routes/web/media.php` | Media manager browser |
| `routes/web/notifications.php` | Notifications UI |
| `routes/web/pages.php` | Documentation, support, changelog views |
| `routes/web/security.php` | User management, audit trail controllers |
| `routes/web/support.php` | Redirects to Telescope / Horizon (permission-gated) |

### Access control pattern

- Admin: `can:access.admin-panel`
- Roles: `can:manage.roles`
- Settings: `can:manage.settings`
- Media: `can:access.media-management`
- Telescope / Horizon: `can:access.telescope`, `can:access.horizon`

### Support folder

- `support/helpers.php` autoloaded from `composer.json` — global helpers without dumping everything into `app/`.

### Documentation & community files (stubs)

Stubs include project docs and governance templates (`docs/`, `CHANGELOG.md`, contributing/code of conduct, etc.) — good for **teams** and open-source consistency.

---

## 5. Deep dive: dev tools config

This section is the **primary focus** for comparing to a mature boilerplate.

### 5.1 Laravel Pint — `stubs/pint.json`

Full contents:

```json
{
    "preset": "laravel",
    "rules": {
        "no_superfluous_phpdoc_tags": false,
        "no_empty_phpdoc": false,
        "phpdoc_no_empty_return": false,
        "phpdoc_no_useless_inheritdoc": false,
        "phpdoc_trim": false,
        "phpdoc_trim_consecutive_blank_line_separation": false,
        "general_phpdoc_annotation_remove": false,
        "phpdoc_annotation_without_dot": false,
        "phpdoc_summary": false,
        "phpdoc_separation": false,
        "phpdoc_single_line_var_spacing": false,
        "phpdoc_to_comment": false,
        "phpdoc_tag_type": false,
        "phpdoc_var_without_name": false,
        "phpdoc_align": false,
        "phpdoc_indent": false,
        "phpdoc_inline_tag_normalizer": false,
        "phpdoc_no_alias_tag": false,
        "phpdoc_no_package": false,
        "phpdoc_scalar": false,
        "phpdoc_types": false,
        "phpdoc_types_order": false,
        "phpdoc_var_annotation_correct_order": false
    },
    "exclude": [
        "vendor",
        "node_modules",
        "storage",
        "bootstrap/cache"
    ]
}
```

**Interpretation:**

- **Preset:** `laravel` (opinionated Laravel style).
- **PHPDoc:** Almost every PHPDoc-related fixer is **explicitly turned off** (`false`). That avoids churn on docblocks and matches teams that use PHPDoc for static analysis or leave docs “as written.”
- **Excludes:** Standard generated/cache directories.

**CI note:** The GitHub Action uses `aglipanci/laravel-pint-action` with `preset: laravel` — it may **not** read `pint.json` unless the action passes it through; verify in your fork if you rely on the disabled PHPDoc rules in CI.

### 5.2 Rector — `stubs/rector.php`

Full contents:

```php
<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;
use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetList;

return static function (RectorConfig $rectorConfig): void {
    // Paths to analyze
    $rectorConfig->paths([
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/support',
        __DIR__.'/tests',
    ]);

    // Skip specific rules
    $rectorConfig->skip([
        CompactToVariablesRector::class,
    ]);

    // Enable caching for Rector
    $rectorConfig->cacheDirectory(__DIR__.'/storage/rector');
    $rectorConfig->cacheClass(FileCacheStorage::class);

    // Apply sets for Laravel and general code quality
    $rectorConfig->sets([
        LaravelLevelSetList::UP_TO_LARAVEL_120,
        LaravelSetList::LARAVEL_130,
        SetList::CODE_QUALITY,
    ]);

    // Define PHP version for Rector
    $rectorConfig->phpVersion(PhpVersion::PHP_85);
};
```

**Interpretation:**

| Setting | Meaning |
|---------|---------|
| **Paths** | Classic Laravel dirs + `support/` |
| **Skip `CompactToVariablesRector`** | Avoids rewriting `compact()` calls in a way the team dislikes |
| **Cache** | File cache under `storage/rector` |
| **Sets** | Upgrade path through Laravel 12 **and** Laravel 13 rules + general **CODE_QUALITY** |
| **PHP version** | **PHP 8.5** — aggressive modernization target |

**Composer script (injected):** `rector` → `vendor/bin/rector process`

### 5.3 PHPStan / Larastan

- **Packages:** `larastan/larastan` + PHPStan (via Laravel ecosystem).
- **Stub repo state:** A committed `phpstan.neon` / `phpstan.neon.dist` may be **absent** from `stubs/` in some revisions; the **Security** workflow still runs `vendor/bin/phpstan analyse --memory-limit=512M`, so generated projects must define config (often `phpstan.neon` or `phpstan.neon.dist` at root) or CI will fail until you add it.

**Minimal Larastan starter** (from maintainer docs pattern — adjust paths/level):

```neon
includes:
    - vendor/larastan/larastan/extension.neon
parameters:
    level: 0
    paths:
        - app
```

**Recommendation for your boilerplate:** Commit `phpstan.neon.dist`, document level progression (0 → max), and align CI with `phpstan analyse -c phpstan.neon.dist`.

### 5.4 Pest & architecture testing

- **Plugin:** `pestphp/pest-plugin-arch`
- **Example:** [`stubs/tests/Feature/ArchitectureTest.php`](kickoff-main/kickoff-main/stubs/tests/Feature/ArchitectureTest.php) encodes team rules:
  - PHP ≥ 8.4 at runtime
  - No `dd`, `dump`, `ray` in app/config/routes/support
  - No `url()` helper (project-specific rule)
  - Controllers suffixed `Controller`
  - Policies are classes with `Policy` suffix
  - Mailables extend `Mailable`
  - `env()` only in `config/`
  - No raw `DB::` facades in `App\` (push query logic to layer that belongs)
  - Traits / Enums / Interfaces / Middleware conventions

**Composer scripts (injected into generated app):**

| Script | Command |
|--------|---------|
| `analyse` | `phpstan analyse` |
| `test` | `pest` |
| `test-arch` | `pest tests/Feature/ArchitectureTest.php` |
| `test-coverage` | `pest --coverage` |
| `format` | `pint` |
| `lint` | `phplint` (ensure `phplint` is required if you keep this) |
| `rector` | `rector process` |

### 5.5 Kickoff package’s own `composer.json` (meta-tooling)

The **Kickoff** repository (not the generated app) also declares dev tools for maintaining Kickoff itself:

```json
"scripts": {
    "lint": [
        "@php vendor/bin/pint --ansi",
        "@php vendor/bin/phpstan analyse --verbose --ansi"
    ],
    "test": [
        "@php vendor/bin/pest"
    ]
}
```

So: **Pint + PHPStan** for the CLI package source under `src/`.

---

## 6. CI/CD workflows

Workflows ship under **`stubs/.github/workflows/`** — they land in **generated** repos, not necessarily at the root of the Kickoff package clone.

### 6.1 `lint.yml` — Pint on push

- Triggers: **every push**
- Uses `aglipanci/laravel-pint-action@0.1.0` with preset `laravel`
- **`git-auto-commit-action`** commits fixes with message `PHP Linting (Pint)`

**Trade-off:** Auto-commit on push can surprise teams; many orgs prefer **PR-only** lint with required status checks and **no** automatic commits.

### 6.2 `run-tests.yml` — Pest

- Triggers: `push`, `pull_request`
- Matrix: PHP **8.4** and **8.5**, Ubuntu
- `composer update` with scripts skipped at install
- `vendor/bin/pest` with `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`

**Trade-off:** `composer update` in CI can **non-deterministically** change lock resolution vs `composer install` — consider `install --prefer-dist` + committed lock for reproducible builds.

### 6.3 `rector.yml` — Rector gate

- Triggers: push to `main`, PRs to any branch
- PHP 8.5, `composer install`
- Caches **`.rector/cache`** in the workflow (see [§11](#11-gaps-and-repo-realities) for config vs cache path)
- Runs `vendor/bin/rector process`
- **`git diff --exit-code`** — fails if Rector would change the tree (**strong quality gate**)

### 6.4 `security.yml` — Audit + PHPStan

- Triggers: `push`, `pull_request`
- PHP 8.4
- `composer update` (same reproducibility note)
- `composer audit`
- `vendor/bin/phpstan analyse --memory-limit=512M`

### 6.5 `update-changelog.yml` — Release automation

- On **release published**
- Checks out `main`, runs `changelog-updater-action`, commits `CHANGELOG.md`

---

## 7. Automation scripts (`stubs/bin/`)

Shell scripts for **ops** (paths relative to generated project root). Names are placeholders until `${PROJECT_NAME}` is replaced.

| Script | Role |
|--------|------|
| **`install`** | Create MySQL DB from folder name, patch `.env.example`, `composer install`, `npm` build, copy `.env`, `key:generate` |
| **`deploy`** | Deploy by branch/tag: maintenance mode, `composer install --no-dev`, clear caches, migrate, **Horizon terminate**, `chown`, health check `GET /up`, **rollback** on failure |
| **`backup-app`** | Rsync-style app backup |
| **`backup-db`** | mysqldump backup |
| **`backup-media`** | Media backup (e.g. recent changes) |
| **`build-fe-assets`** | NPM build + optional git commit of `public/` |
| **`update-dependencies`** | Composer/npm updates, audit, build |
| **`reinstall-npm`** | Clean npm reinstall |

**Pattern:** Encode **backup → deploy → health check → rollback** — valuable for production Laravel playbooks.

### Maintainer sandbox (`bin/sandbox` at Kickoff package root)

Not copied into apps — used to **test Kickoff itself**: creates `test-output/sandbox` via `laravel new`, runs `kickoff start sandbox sandbox`, optional seeding. Uses git `skip-worktree` on `test-output` to avoid accidental commits.

---

## 8. Infrastructure templates

### `stubs/docker-compose.yml`

Services (from file header — typical stack):

- **MySQL 8.4** — persistent volume, healthcheck
- **Redis** — password optional, healthcheck
- **Mailpit** — SMTP + web UI
- **Meilisearch** — search API
- **MinIO** — S3-compatible storage with console

Named volumes for data durability.

### `.config/` templates

Stubs include **Nginx** (e.g. MinIO) and **Supervisor** templates for queue workers — adapt to your hosting.

---

## 9. Testing setup

### `stubs/phpunit.xml`

- Test suites: `tests/Unit`, `tests/Feature`
- Code coverage source: `app`
- Env: `APP_ENV=testing`, SQLite `:memory:`, `TELESCOPE_ENABLED=false`, `PULSE_ENABLED=false`, sync queue, array cache/session

### Pest in CI

Workflow sets SQLite env vars explicitly for `vendor/bin/pest`.

### Architecture tests

See [§5.4](#54-pest--architecture-testing) — treat `ArchitectureTest.php` as a **living constitution** for your codebase.

---

## 10. What to adopt — key takeaways

For a **mature boilerplate**, high-value patterns from Kickoff:

1. **Rector CI with `git diff --exit-code`** — prevents drifting from automated upgrades; developers run Rector locally and commit.
2. **Pint with PHPDoc rules off** — less noise if you rely on Larastan for types or prefer minimal doc churn.
3. **`require_all_in` route splitting** — scales better than monolithic `web.php`.
4. **Composer scripts** for `analyse`, `test`, `test-arch`, `format`, `rector`, and a **`dev` concurrently** script — one-command local workflow.
5. **Pest Arch** rules tailored to your team (ban `env()` outside config, ban debug helpers in production paths, etc.).
6. **Deploy script** with backup + `/up` health check + rollback.
7. **docker-compose** for MySQL, Redis, Mailpit, Meilisearch, MinIO — parity between dev and prod services.
8. **Separate product site** (like kickoff.my) for onboarding and SEO; keep README technical.

---

## 11. Gaps and repo realities

When mirroring Kickoff, watch for these:

| Topic | Detail |
|-------|--------|
| **PHPStan config in stubs** | May be missing in tree; **Security** workflow expects PHPStan to run — add `phpstan.neon.dist` to your boilerplate explicitly. |
| **Rector cache path** | `rector.php` uses `storage/rector`; `rector.yml` caches `.rector/cache`. Align paths for effective CI caching. |
| **`lint` Composer script** | Generated app maps `lint` to **`phplint`**, not Pint — ensure `phplint` is installed or rename to avoid confusion (`format` = Pint). |
| **Pint Action vs `pint.json`** | Confirm the GitHub Action respects your JSON overrides. |
| **README vs code** | README may mention files not present in every revision; treat the repository as source of truth. |

---

## References

- Product / docs: [kickoff.my](https://kickoff.my/)
- Package: [cleaniquecoders/kickoff](https://packagist.org/packages/cleaniquecoders/kickoff) (`composer global require cleaniquecoders/kickoff`)
- Laravel installer: `composer global require laravel/installer`
- Companion packages (studied for this doc): [cleaniquecoders/laravel-media-secure](https://github.com/cleaniquecoders/laravel-media-secure), [cleaniquecoders/traitify](https://github.com/cleaniquecoders/traitify)
- Local clone paths in this workspace: [`kickoff-main/kickoff-main/`](kickoff-main/kickoff-main/) (package + `stubs/`)

---

*Document generated for boilerplate improvement; verify versions and file paths against your pinned Kickoff commit.*
