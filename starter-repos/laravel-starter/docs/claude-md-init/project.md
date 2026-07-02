# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

> Generated with `/init` on the Laravel starter, then reviewed. Kept the accurate
> scan of the current codebase; trimmed generic boilerplate and corrected a couple
> of details `/init` inferred wrong (noted in `docs/claude-md-notes.md`).

## Development Commands

```bash
# Install dependencies
composer install

# Environment (first run)
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate

# Run the app
php artisan serve            # http://localhost:8000

# Tests
php artisan test            # full PHPUnit suite
php artisan test --compact  # condensed output
php artisan test --filter=test_authenticated_user_can_create_a_task

# Formatting
vendor/bin/pint             # Laravel Pint (PSR-12 based)

# Queue worker
php artisan queue:work      # QUEUE_CONNECTION defaults to sync
```

## Tech Stack

- **PHP** 8.5
- **Laravel** 12 (streamlined application structure)
- **filament/filament** v5 (admin panel scaffolding)
- **livewire/livewire** v4
- **laravel/sanctum** v4
- **laravel/boost** v2 (MCP server; see `.mcp.json`)
- **laravel/pint** v1 (formatter)
- **phpunit/phpunit** v11 (testing)
- **Database:** SQLite
- **Frontend:** Blade templates with Tailwind CSS via CDN

## Project Structure

```
app/
  Http/Controllers/
    AuthController.php     # login, register, logout (session auth)
    Controller.php        # base controller
    TaskController.php     # Task resource CRUD
  Models/
    Task.php              # belongsTo User
    User.php              # hasMany Task; Notifiable
  Providers/
    AppServiceProvider.php
    Filament/AppPanelProvider.php
bootstrap/
  app.php                 # middleware, exceptions, routing (Laravel 11/12 style)
  providers.php
database/
  factories/UserFactory.php
  migrations/             # users, password_reset_tokens, sessions, tasks
routes/
  web.php                 # auth + tasks resource routes
  console.php
resources/views/
  auth/{login,register}.blade.php
  layouts/app.blade.php
  tasks/{index,create,show,edit}.blade.php
tests/
  Feature/TaskTest.php
  Unit/
  TestCase.php
```

## Architecture

### Authentication
Hand-authored session-based auth in `AuthController` (no Breeze/Jetstream).
`login()` uses `Auth::attempt()` and regenerates the session; `register()` creates
the user and calls `Auth::login()`; `logout()` invalidates the session. Routes are
grouped by `guest` and `auth` middleware in `routes/web.php`.

### Data Model
- **User**: `name`, `email`, `password` (hashed cast). `hasMany(Task)`. Uses the
  `Notifiable` trait.
- **Task**: `title`, `description` (nullable), `status` (enum `todo|doing|done`,
  default `todo`), `user_id`. `belongsTo(User)`.

### Authorization
`TaskController` scopes every query to the authenticated user. `index()` reads
`Auth::user()->tasks()`; `store()` creates through the relationship;
`show/edit/update/destroy` call `authorizeTask()`, which `abort(403)`s if the
task's `user_id` does not match `Auth::id()`.

### Routing
`routes/web.php` defines a `guest` group (login/register), an `auth`-protected
`logout`, and `Route::resource('tasks', TaskController::class)` inside an `auth`
group. `/` redirects to `/tasks`.

## Testing

- PHPUnit 11. Tests extend `Tests\TestCase` and use `RefreshDatabase`.
- `phpunit.xml` runs against **in-memory SQLite** (`DB_DATABASE=:memory:`) with
  `array` cache/session/mail drivers and `BCRYPT_ROUNDS=4`.
- Two suites are configured: `Unit` (`tests/Unit`) and `Feature` (`tests/Feature`).
- Current coverage: `tests/Feature/TaskTest.php` (create a task).
- Use model factories; only `UserFactory` exists so far.

## Conventions

- Follow existing code conventions; check sibling files for structure and naming.
- Use `php artisan make:*` generators for new files.
- Run `vendor/bin/pint` after modifying PHP files.
- Laravel 12: register middleware/exceptions/routing in `bootstrap/app.php`; there
  is no `app/Http/Kernel.php` or `app/Console/Kernel.php`.
- `Task.status` values are lowercase (`todo`, `doing`, `done`).
