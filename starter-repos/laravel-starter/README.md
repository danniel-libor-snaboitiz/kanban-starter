# Laravel Starter

Minimal Laravel 11 starter used as the base for a Claude Code training course. Students build a Kanban board feature on top of it.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

Open http://localhost:8000.

## Routes

- `GET /login` and `POST /login`
- `GET /register` and `POST /register`
- `POST /logout`
- `GET /tasks` (and full resource: `tasks.create`, `tasks.store`, `tasks.show`, `tasks.edit`, `tasks.update`, `tasks.destroy`)

## Stack notes

- Session-based auth with a hand-authored `AuthController` (no Breeze / Jetstream).
- One CRUD entity: `Task` (title, description, status: todo | doing | done) owned by a `User`.
- Tailwind is loaded via CDN in `resources/views/layouts/app.blade.php`. No npm build required.
- Default database is SQLite.
- One passing feature test in `tests/Feature/TaskTest.php`.

## Tests

```bash
php artisan test
```
