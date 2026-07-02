# Project CLAUDE.md — Kanban Board (Laravel 11)

## Stack

- Laravel 11, PHP 8.3
- SQLite dev / Postgres prod
- Blade + Tailwind (CDN in dev)
- Alpine.js for drag-drop
- PHPUnit for tests

## Layout

- `app/Models/` — Board, Column, Card, Comment, Notification, User
- `app/Http/Controllers/` — one controller per resource, RESTful
- `app/Services/MentionParser.php` — pure function, unit-tested
- `resources/views/` — Blade templates, one folder per resource
- `tests/Feature/` — feature tests (authenticated flows)
- `tests/Unit/MentionParserTest.php` — pure-logic tests

## Conventions

- Route names use dot notation: `boards.index`, `cards.store`.
- Every mutation route lives behind `auth` middleware.
- Every controller method that touches a Board/Column/Card starts with an `authorize()` check.
- Migrations named `YYYY_MM_DD_HHMMSS_verb_noun_table.php`.
- Never call Eloquent inside a Blade view — pass data via the controller.

## Commands

- Run app: `php artisan serve`
- Run tests: `php artisan test`
- Fresh DB: `php artisan migrate:fresh --seed`
- Tail logs: `tail -f storage/logs/laravel.log`

## Style

- PHP: follow the existing PSR-12; `./vendor/bin/pint` to autoformat.
- Blade: two-space indent, one component per file.

## What to be careful about

- The @mention parser has to handle usernames with dots and underscores. Test both.
- The notifications feed will be queried on every page (nav badge). Keep the query indexed on `(user_id, read_at)`.
- Drag-drop reordering fires many small PATCH requests. Batch them if possible.

## Where to look when you're stuck

- Look at `tests/Feature/BoardTest.php` for auth patterns.
- Look at `app/Services/MentionParser.php` for the pattern I use for pure-logic services.
