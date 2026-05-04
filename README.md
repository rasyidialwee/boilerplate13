# Laravel 12 React Starter Kit

A modern Laravel 12 application with React and TypeScript, powered by Inertia.js and Tailwind CSS. This project uses Laravel Sail for a seamless Docker-based development environment.

## 🚀 Tech Stack

- **Backend**: Laravel 12 (PHP 8.4+)
- **Frontend**: React 19 with TypeScript
- **Framework**: Inertia.js
- **Styling**: Tailwind CSS 4
- **Build Tool**: Vite
- **Containerization**: Laravel Sail (Docker)
- **Database**: MySQL 8.0
- **Cache/Sessions**: Redis
- **Queue**: Redis (via Laravel Horizon)
- **Websockets**: Laravel Reverb
- **Email Testing**: Mailpit
- **Code Quality**: Larastan (PHPStan), Rector
- **Permissions**: Spatie Laravel Permission
- **Frontend Tools**: ESLint, Prettier
- **Form Validation**: Laravel Precognition (real-time validation)

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- [Docker Desktop](https://www.docker.com/products/docker-desktop) or Docker Engine with Docker Compose
- [Git](https://git-scm.com/downloads)
- (Optional) [Node.js](https://nodejs.org/) and [Composer](https://getcomposer.org/) if you prefer not to use Sail

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd boilerplate12
```

### 2. Install Dependencies with Composer

**First-time setup** - Run this command to install Composer dependencies using Docker (required before using Sail):

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

### 3. Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

### 4. Generate Application Key

```bash
sail artisan key:generate
```

### 5. Install Node Dependencies

```bash
sail npm install
```

### 6. Build Assets

```bash
sail npm run dev
```

### 7. Run Database Migrations and seeding

```bash
sail artisan migrate --seed
```

or run migrate:fresh for re seed

```bash
sail artisan migrate:fresh --seed
```

This seeds roles (`superadmin`, `admin`, `user`) and canonical permissions from [`database/seeders/RolePermissionSeeder.php`](database/seeders/RolePermissionSeeder.php). Assign roles or permissions as needed for each environment.

## 🏃 Starting Development

### Using Laravel Sail (Recommended)

Start all services:

```bash
./vendor/bin/sail up -d
```

Or use the shorter alias (if configured):

```bash
sail up -d
```

This will start:

- **Laravel Application**: `http://localhost` (or port specified in `APP_PORT`)
- **Vite Dev Server**: `http://localhost:5173`
- **MySQL**: Port `3306`
- **Redis**: Port `6379`
- **Mailpit Dashboard**: `http://localhost:8025`

**Note**: Reverb websocket server should be started separately with `sail artisan reverb:start`


# Liner, Code Format and Types Check
```
# Linter
sail npm run lint

# Format code
sail npm run format

# Type checking
sail npm run types
```
## 🔧 Available Services

When running `sail up`, the following services are available:

| Service         | URL/Port                     | Description                                           |
| --------------- | ---------------------------- | ----------------------------------------------------- |
| **Laravel App** | `http://localhost`           | Main application                                      |
| **Vite**        | `http://localhost:5173`      | Frontend development server                           |
| **MySQL**       | `localhost:3306`             | Database server                                       |
| **Redis**       | `localhost:6379`             | Cache and session store                               |
| **Mailpit**     | `http://localhost:8025`      | Email testing dashboard                               |
| **Horizon**     | `http://localhost/horizon`   | Queue management dashboard (requires permission)      |
| **Telescope**   | `http://localhost/telescope` | Application debugging dashboard (requires permission) |
| **Reverb**      | `localhost:8080`             | WebSocket server (run via `artisan reverb:start`)     |

## 📝 Environment Configuration

Key environment variables to configure in your `.env` file:

```env
APP_NAME=Laravel
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

# Queue Configuration (for Horizon)
QUEUE_CONNECTION=redis

# Broadcasting Configuration (for Reverb)
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=app-id
REVERB_APP_KEY=app-key
REVERB_APP_SECRET=app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Telescope Configuration
TELESCOPE_ENABLED=true

# Horizon Configuration
HORIZON_PREFIX=horizon
HORIZON_BALANCE=auto

# Vite will automatically use the correct host
VITE_APP_NAME="${APP_NAME}"
```

**Note**: When using Laravel Sail, database credentials default to:

- **Username**: `sail`
- **Password**: `password`
- **Database**: Value from `DB_DATABASE` in `.env`

### Code Quality Workflow

Use this as the canonical workflow for code quality checks and fixes.

#### PHP Tooling via Sail

PHP tooling is configured to run through Laravel Sail, so host PHP is not required.

Setup files:
- `.vscode/settings.json`
- `bin/php`
- `bin/verify-php-tooling`
- `.git/hooks/pre-commit`

After pulling changes, ensure scripts are executable:

```bash
chmod +x bin/php bin/verify-php-tooling .git/hooks/pre-commit
```

Verify the full setup:

```bash
./bin/verify-php-tooling
```

#### PHP Tools

```bash
# PHPStan static analysis
./vendor/bin/sail composer phpstan

# Rector preview (dry-run)
./vendor/bin/sail composer rector

# Rector apply fixes
./vendor/bin/sail composer rector:fix

# Format changed PHP files
./vendor/bin/sail pint
```

- `phpstan`: static analysis to detect type and logic issues.
- `rector`: preview code changes without writing files.
- `rector:fix`: apply Rector changes.
- `pint`: format PHP code to project style.

#### Frontend Tools

```bash
# ESLint check (with auto-fix where configured)
sail npm run lint

# Prettier format (writes changes)
sail npm run format

# Prettier check-only (no changes)
sail npm run format:check
```

- `lint`: runs ESLint rules for JS/TS/React files.
- `format`: rewrites files to Prettier style.
- `format:check`: verifies formatting in CI/check mode.

ESLint is configured in `eslint.config.js` with React, TypeScript, and Prettier integration. Prettier is configured in `.prettierrc` with Tailwind CSS plugin support.

### Laravel Precognition

This project uses [Laravel Precognition](https://laravel.com/docs/12.x/precognition) for real-time form validation. Precognition provides instant validation feedback as users type, without requiring a full form submission.







### Queue Management (Laravel Horizon)

Horizon provides a dashboard and monitoring for your Redis queues. Access it at `http://localhost/horizon` (requires permission: `view_horizon`).

```bash
# Start Horizon
sail artisan horizon

# Pause Horizon
sail artisan horizon:pause

# Continue Horizon
sail artisan horizon:continue

# Terminate Horizon
sail artisan horizon:terminate

# View Horizon status
sail artisan horizon:status
```

**Note**: Only users with the `view_horizon` permission can access the Horizon dashboard (included on the `superadmin` role by default; extend seeders or roles if others should see it).

### Application Debugging (Laravel Telescope)

Telescope provides insights into your application's requests, commands, jobs, and more. Access it at `http://localhost/telescope` (requires permission: `view_telescope`).

Telescope is enabled in both development and production environments. Access is controlled via Spatie permissions — users must have the `view_telescope` permission.

**Note**: Only users with the `view_telescope` permission can access the Telescope dashboard (included on the `superadmin` role by default).

### WebSockets (Laravel Reverb)

Reverb provides a WebSocket server for real-time features. Start it with:

```bash
# Start Reverb server
sail artisan reverb:start

# Start Reverb in development mode (with debugging)
sail artisan reverb:start --debug

# Start Reverb with specific host and port
sail artisan reverb:start --host=0.0.0.0 --port=8080
```

Reverb configuration is in `config/reverb.php` and can be customized via environment variables in `.env`.

### Permissions (Spatie Laravel Permission)

This project uses Spatie Laravel Permission. All permission names are **snake_case** and defined in `RolePermissionSeeder` (run `sail artisan migrate --seed` or `db:seed` after changes).

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
sail artisan permission:create-permission "permission name"

# Create a role
sail artisan permission:create-role "role name"

# Assign a role to a user
sail artisan permission:assign-role

# Show a table of roles and permissions
sail artisan permission:show

# Reset the permission cache
sail artisan permission:cache-reset
```

## 🛑 Stopping Services

To stop all Sail services:

```bash
sail down
```

To stop and remove volumes (⚠️ this will delete database data):

```bash
sail down -v
```
## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sail Documentation](https://laravel.com/docs/sail)
- [Laravel Horizon Documentation](https://laravel.com/docs/horizon)
- [Laravel Telescope Documentation](https://laravel.com/docs/telescope)
- [Laravel Reverb Documentation](https://laravel.com/docs/reverb)
- [Inertia.js Documentation](https://inertiajs.com/)
- [React Documentation](https://react.dev/)
- [Vite Documentation](https://vitejs.dev/)
- [Spatie Laravel Permission Documentation](https://spatie.be/docs/laravel-permission)
- [ESLint Documentation](https://eslint.org/)
- [Prettier Documentation](https://prettier.io/)
- [Larastan Documentation](https://github.com/larastan/larastan)
- [Rector Laravel Documentation](https://github.com/driftingly/rector-laravel)
- [Laravel Precognition Documentation](https://laravel.com/docs/12.x/precognition)

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
