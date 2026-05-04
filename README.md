# Laravel 12 React Starter Kit

A modern Laravel 12 application with React and TypeScript, powered by Inertia.js and Tailwind CSS. This project uses Laravel Sail for a seamless Docker-based development environment.

## 🚀 Tech Stack

- **Backend**: Laravel 12 (PHP **^8.2** in `composer.json`; the Sail image ships **PHP 8.4**)
- **Frontend**: React 19 with TypeScript
- **Framework**: Inertia.js
- **Routing / client URLs**: [Laravel Wayfinder](https://github.com/laravel/wayfinder) (generated actions and route helpers)
- **Styling**: Tailwind CSS 4
- **Build Tool**: Vite
- **Containerization**: Laravel Sail (Docker)
- **Database**: MySQL 8.0
- **Cache/Sessions**: Redis
- **Queue**: Redis (via Laravel Horizon)
- **Websockets**: Laravel Reverb
- **Email Testing**: Mailpit
- **Code Quality**: Larastan (PHPStan), Rector, Laravel Pint
- **Testing**: Pest 4, Pest architecture plugin (`pestphp/pest-plugin-arch`)
- **Permissions**: Spatie Laravel Permission
- **Activity**: Spatie Laravel Activity Log
- **API filtering**: Spatie Laravel Query Builder
- **Settings**: Spatie Laravel Settings
- **Media & uploads**: [Spatie Laravel Media Library](https://spatie.be/docs/laravel-medialibrary), [Laravel Media Secure](https://github.com/cleaniquecoders/laravel-media-secure), [Traitify](https://github.com/cleaniquecoders/traitify) (shared UUIDs on models)
- **Frontend Tools**: ESLint, Prettier
- **Form Validation**: Laravel Precognition (real-time validation)
- **DX (dev)**: Laravel Boost, Laravel Pail

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- [Docker Desktop](https://www.docker.com/products/docker-desktop) or Docker Engine with Docker Compose
- [Git](https://git-scm.com/downloads)
- (Optional) [Node.js](https://nodejs.org/) and [Composer](https://getcomposer.org/) if you prefer not to use Sail

Commands below use **`./vendor/bin/sail`**. If you use a shell alias, you can substitute `sail` instead.

## 🛠️ Installation

### 1. Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

### 2. Copy compose-dev.yaml

Copy the example compose file:

```bash
cp compose-dev.yaml compose.yaml
```

### 3. Install Dependencies with Composer

**First-time setup** — run this to install Composer dependencies with Docker (required before Sail is available):

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

### 5. Starting Development

Start Docker services:

```bash
./vendor/bin/sail up -d
```

Or use a shorter alias if you have one configured:

```bash
sail up -d
```

### 4. Generate Application Key

```bash
./vendor/bin/sail artisan key:generate
```

### 5. Install Node Dependencies

```bash
./vendor/bin/sail npm install
```

### 6. Build or Run Frontend Assets

```bash
./vendor/bin/sail npm run dev
```

### 7. Run Database Migrations and Seeding

Initial migrate and seed:

```bash
./vendor/bin/sail artisan migrate --seed
```

This seeds roles (`superadmin`, `admin`, `user`) and canonical permissions from [`database/seeders/RolePermissionSeeder.php`](database/seeders/RolePermissionSeeder.php). Assign roles or permissions as needed for each environment.




This typically exposes:

- **Laravel application**: `http://localhost` (or the host/port from `APP_URL` / `APP_PORT` in `.env`)
- **Vite dev server**: `http://localhost:5173` (see `VITE_*` in `.env` if you customize ports)
- **MySQL**: `localhost:3306`
- **Redis**: `localhost:6379`
- **Mailpit**: `http://localhost:8025`

**One-command local stack** (PHP built-in server, queue listener, and Vite via `concurrently`):

```bash
./vendor/bin/sail composer run dev
```

That runs the `dev` script from `composer.json` inside Sail. Start dependencies first with `./vendor/bin/sail up -d` (MySQL, Redis, etc.). For a more traditional flow, keep containers up and run `./vendor/bin/sail npm run dev` in another terminal instead of or in addition to this script, depending on how you serve PHP (e.g. Nginx in Sail vs. `artisan serve` in `dev`).

**Reverb** is not started by `sail up` by default. Start it when you need websockets:

```bash
./vendor/bin/sail artisan reverb:start
```

## 🔧 Available Services

When `./vendor/bin/sail up` is running, typical services are:

| Service         | URL/Port                     | Description                                           |
| --------------- | ---------------------------- | ----------------------------------------------------- |
| **Laravel App** | `http://localhost`           | Main application (respects `APP_PORT` / `APP_URL`)   |
| **Vite**        | `http://localhost:5173`      | Frontend development server                           |
| **MySQL**       | `localhost:3306`             | Database server                                       |
| **Redis**       | `localhost:6379`             | Cache and session store                               |
| **Mailpit**     | `http://localhost:8025`      | Email testing dashboard                               |
| **Horizon**     | `http://localhost/horizon`   | Queue management dashboard (requires permission)      |
| **Telescope**   | `http://localhost/telescope` | Application debugging dashboard (requires permission) |
| **Reverb**      | `localhost:8080`             | WebSocket server (run via `artisan reverb:start`)     |

## Code Quality Workflow
### PHP (Composer scripts via Sail)

```bash
# PHPStan (Larastan) static analysis
./vendor/bin/sail composer phpstan

# Rector dry-run (no file changes)
./vendor/bin/sail composer rector:dry

# Rector apply refactors (writes files)
./vendor/bin/sail composer rector
# same effect:
./vendor/bin/sail composer rector:fix

# Composer "lint": runs PHPStan then Rector dry-run
./vendor/bin/sail composer lint

# Format PHP with Pint (project style)
./vendor/bin/sail bin pint
# format only files changed against git:
./vendor/bin/sail bin pint --dirty

# Run the test suite
./vendor/bin/sail composer test

# Architecture-focused Pest file
./vendor/bin/sail composer test-arch
```

- **`phpstan`**: static analysis for types and common issues.
- **`rector:dry`**: preview Rector changes without writing files.
- **`rector`** / **`rector:fix`**: both run `rector process` and **apply** changes — commit or review the diff afterward.
- **`lint`**: runs `phpstan` then `rector:dry` (good for a quick gate before push).
- **`format`** (Composer): runs Pint **on the host** via `composer format` (`vendor/bin/pint`), not inside the Sail app container. Prefer **`./vendor/bin/sail bin pint`** when you want formatting inside the same environment as the rest of Sail.

Architecture rules live in [`tests/Unit/ArchitectureTest.php`](tests/Unit/ArchitectureTest.php).

### Frontend (npm via Sail)

```bash
# ESLint (with auto-fix where configured)
./vendor/bin/sail npm run lint

# Prettier — write formatted files
./vendor/bin/sail npm run format

# Prettier — check only (CI-style)
./vendor/bin/sail npm run format:check

# TypeScript check
./vendor/bin/sail npm run types
```

- **`lint`**: ESLint for JS/TS/React.
- **`format`**: Prettier write mode.
- **`format:check`**: Prettier without modifying files.
- **`types`**: TypeScript compiler check.

ESLint is configured in `eslint.config.js` with React, TypeScript, and Prettier integration. Prettier is configured in `.prettierrc` with Tailwind CSS plugin support.

## Laravel Precognition

This project uses [Laravel Precognition](https://laravel.com/docs/12.x/precognition) for real-time form validation. Precognition provides instant validation feedback as users type, without requiring a full form submission.

## Queue Management (Laravel Horizon)

Horizon provides a dashboard and monitoring for your Redis queues. Access it at `http://localhost/horizon` (or your `APP_URL` equivalent; requires permission: `view_horizon`).

```bash
# Start Horizon
./vendor/bin/sail artisan horizon

# Pause Horizon
./vendor/bin/sail artisan horizon:pause

# Continue Horizon
./vendor/bin/sail artisan horizon:continue

# Terminate Horizon
./vendor/bin/sail artisan horizon:terminate

# View Horizon status
./vendor/bin/sail artisan horizon:status
```

**Note**: Only users with the `view_horizon` permission can access the Horizon dashboard (included on the `superadmin` role by default; extend seeders or roles if others should see it).

## Application Debugging (Laravel Telescope)

Telescope provides insights into your application's requests, commands, jobs, and more. Access it at `http://localhost/telescope` (requires permission: `view_telescope`).

Telescope is enabled in both development and production environments. Access is controlled via Spatie permissions — users must have the `view_telescope` permission.

**Note**: Only users with the `view_telescope` permission can access the Telescope dashboard (included on the `superadmin` role by default).

## WebSockets (Laravel Reverb)

Reverb provides a WebSocket server for real-time features. Start it with:

```bash
# Start Reverb server
./vendor/bin/sail artisan reverb:start

# Start Reverb in development mode (with debugging)
./vendor/bin/sail artisan reverb:start --debug

# Start Reverb with specific host and port
./vendor/bin/sail artisan reverb:start --host=0.0.0.0 --port=8080
```

Reverb configuration is in `config/reverb.php` and can be customized via environment variables in `.env`.

## Permissions (Spatie Laravel Permission)

This project uses Spatie Laravel Permission. All permission names are **snake_case** and defined in `RolePermissionSeeder` (run `./vendor/bin/sail artisan migrate --seed` or `db:seed` after changes).

**Seeded roles (default):**

- `superadmin` — all permissions
- `admin` — `manage_system_settings`, `view_activity_logs`, and full user CRUD permissions (`view_users`, `create_users`, `edit_users`, `delete_users`)
- `user` — no extra permissions

**Seeded permissions (default):**

- `view_telescope`, `view_horizon` — dashboard access (only `superadmin` by default)
- `manage_system_settings` — system settings page
- `view_activity_logs` — activity log UI
- `view_users`, `create_users`, `edit_users`, `delete_users` — user management
- `manage_roles` — role management (only `superadmin` by default)

**Other Spatie permission commands:**

```bash
# Create a single permission
./vendor/bin/sail artisan permission:create-permission "permission name"

# Create a role
./vendor/bin/sail artisan permission:create-role "role name"

# Assign a role to a user
./vendor/bin/sail artisan permission:assign-role

# Show a table of roles and permissions
./vendor/bin/sail artisan permission:show

# Reset the permission cache
./vendor/bin/sail artisan permission:cache-reset
```

## Media uploads (Spatie Media Library, Media Secure, Traitify)

Uploads are modeled with **Spatie Media Library**. Secure URLs (`/media/{type}/{uuid}`) are handled by **Laravel Media Secure**, which delegates authorization to your **parent model policy** when `strict` mode is enabled (see `config/laravel-media-secure.php`). The sample **`Document`** model uses **Traitify** for a `uuid` column and registers a **single-file** `default` collection.

**Setup:**

```bash
./vendor/bin/sail artisan migrate
```

Publish / vendor assets (already committed where applicable):

```bash
./vendor/bin/sail artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
./vendor/bin/sail artisan vendor:publish --tag="media-secure-config"
./vendor/bin/sail artisan vendor:publish --provider="CleaniqueCoders\Traitify\TraitifyServiceProvider"
```

**Useful env keys** (optional overrides — defaults are in `config/laravel-media-secure.php`):

```env
LARAVEL_MEDIA_SECURE_REQUIRE_AUTH=true
LARAVEL_MEDIA_SECURE_STRICT=true
LARAVEL_MEDIA_SECURE_SIGNED_ENABLED=true
LARAVEL_MEDIA_SECURE_SIGNED_EXPIRATION=60
```

**Further reading:**

- [Spatie Laravel Media Library](https://spatie.be/docs/laravel-medialibrary)
- [Laravel Media Secure](https://github.com/cleaniquecoders/laravel-media-secure)
- [Traitify](https://github.com/cleaniquecoders/traitify)

## 🛑 Stopping Services

To stop all Sail services:

```bash
./vendor/bin/sail down
```

To stop and remove volumes (this will delete database data):

```bash
./vendor/bin/sail down -v
```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sail Documentation](https://laravel.com/docs/sail)
- [Laravel Horizon Documentation](https://laravel.com/docs/horizon)
- [Laravel Telescope Documentation](https://laravel.com/docs/telescope)
- [Laravel Reverb Documentation](https://laravel.com/docs/reverb)
- [Laravel Wayfinder](https://github.com/laravel/wayfinder)
- [Inertia.js Documentation](https://inertiajs.com/)
- [React Documentation](https://react.dev/)
- [Vite Documentation](https://vitejs.dev/)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Spatie Laravel Activity Log](https://spatie.be/docs/laravel-activitylog)
- [Spatie Laravel Query Builder](https://spatie.be/docs/laravel-query-builder)
- [Spatie Laravel Settings](https://github.com/spatie/laravel-settings)
- [Spatie Laravel Media Library](https://spatie.be/docs/laravel-medialibrary)
- [Laravel Media Secure (GitHub)](https://github.com/cleaniquecoders/laravel-media-secure)
- [Traitify (GitHub)](https://github.com/cleaniquecoders/traitify)
- [ESLint Documentation](https://eslint.org/)
- [Prettier Documentation](https://prettier.io/)
- [Larastan Documentation](https://github.com/larastan/larastan)
- [Rector Laravel Documentation](https://github.com/driftingly/rector-laravel)
- [Laravel Precognition Documentation](https://laravel.com/docs/12.x/precognition)

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
