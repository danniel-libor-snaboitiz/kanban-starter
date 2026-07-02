# Project CLAUDE.md (manual)

Hand-written project memory for the **Kanban board** feature built on the Laravel
starter. This version is deliberately lean: it front-loads the few things a fresh
model would get wrong, and trusts it to read the code for the rest. See
`docs/claude-md-init/project.md` for the generated version.

## What this project is

A single-user Kanban board with comment threads and `@mention` notifications,
built on top of a minimal Laravel `Task` CRUD starter. Domain being added:
**Board → Column → Card → Comment → Notification**, all scoped to one owning user.

## Stack (what's actually installed — don't assume older versions)

- PHP **8.5**, Laravel **12** (streamlined structure), PHPUnit **11**.
- UI is plain **Blade + Tailwind** (CDN). No Filament, no Livewire, no npm build —
  Filament was removed from this starter, so don't reach for admin-panel scaffolding.
- Laravel **Boost** MCP server is configured (`.mcp.json`, `boost.json`).
- SQLite everywhere.

## Commands

```bash
composer install
php artisan migrate                       # after: touch database/database.sqlite
php artisan serve                         # http://localhost:8000
php artisan test --compact                # full suite (PHPUnit)
php artisan test --compact --filter=Name  # one test while iterating
vendor/bin/pint --dirty --format agent    # format changed PHP before finishing
php artisan queue:work                    # background worker (not needed in Track 1)
```

## The things you would otherwise get wrong

- **`Task.status` is a lowercase enum: `todo | doing | done`.** It's a DB-level
  `enum` column *and* validated with `in:todo,doing,done`. New status-like fields
  (e.g. a card's column) should follow the same lowercase convention. Any card/
  column model added for the Kanban feature must match this casing.
- **Auth is hand-rolled — there is no Breeze/Jetstream.** Session-based, in
  `app/Http/Controllers/AuthController.php` (`Auth::attempt`, `Auth::login`,
  session regenerate/invalidate). Route protection is the `guest` / `auth`
  middleware groups in `routes/web.php`. Extend that pattern; don't scaffold.
- **Ownership scoping IS the security model.** Every read/write is constrained to
  the current user. See `TaskController::authorizeTask()` — it `abort(403)`s when
  `$task->user_id !== Auth::id()`. Replicate this for Board/Column/Card/Comment
  ("I can only mutate my own boards"). This is a graded TDD target — write the
  403 test first.
- **`User` already `use`s the `Notifiable` trait.** Convenient for the `@mention`
  notification feature, but the assignment asks for an explicit `Notification`
  table + `/notifications` feed, so build the domain model rather than leaning on
  Laravel's `notifications` table unless you decide otherwise (document it).
- **Tests run against in-memory SQLite, not your dev file.** `phpunit.xml` sets
  `DB_DATABASE=:memory:` with `array` cache/session/mail and `BCRYPT_ROUNDS=4`.
  Use `RefreshDatabase` and model factories. Only `UserFactory` exists today —
  add factories for new models.
- **Tailwind is CDN-loaded** in `resources/views/layouts/app.blade.php`. No npm
  build step — don't add one.
- **`tests/Unit/` must exist** or PHPUnit aborts the whole run ("Test directory
  not found"). It's kept alive with a `.gitkeep`; put unit tests there (e.g. the
  mention parser).

## Where things live

- Routes: `routes/web.php` (guest group, `auth` group, `Route::resource`).
- Controllers: `app/Http/Controllers/` · Models: `app/Models/` (`Task`, `User`).
- Migrations: `database/migrations/` · Factories: `database/factories/`.
- Views: `resources/views/{auth,tasks,layouts}` (Blade).
- Laravel 12: middleware/exceptions/routing are wired in `bootstrap/app.php`
  (no `app/Http/Kernel.php`, no `app/Console/Kernel.php`).

## Workflow expectations for this assignment

- TDD is graded on git evidence: commit a failing `test:` *before* the `feat:`.
  Required targets: mention parsing, notification creation, ownership authz.
- Use `php artisan make:*` generators for new files; add a factory + (if useful)
  seeder with each new model.
- Run `vendor/bin/pint` before finalizing any PHP change.
