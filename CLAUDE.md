# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this repository is

This is **course material for a Claude Code training program**, not a shippable product. It has two parts:

- **`track-1-beginner/` and `track-2-advanced/`** — self-contained assignment packets (`ASSIGNMENT.md`, `RUBRIC.md`, `SUBMISSION_CHECKLIST.md`, `examples/`). Track 1 builds a Kanban board with comments + @mention notifications; Track 2 builds an automation rules engine (condition trees, action executor, webhook retries) on a background worker. Both drive students through a fixed workflow: CLAUDE.md files → design → plan (subagent) → architecture (subagent/plugin) → **TDD** → self-contained HTML workflow report.
- **`starter-repos/`** — three parallel minimal apps (Laravel, FastAPI, Next.js). A student picks **one** and builds the assignment feature on top of it. Each is an independent project with its own README, dependencies, and test command.

The three starters are deliberately equivalent: each is a single-entity **`Task`** CRUD app (title, description, status) owned by a `User`, with **hand-rolled auth** (no scaffolding libraries), backed by SQLite. When editing course content, keep the three in parity — a change to what students build should hold across all three stacks.

`examples/` inside each track holds **sample student deliverables** (a reference `sample-project-CLAUDE.md`, `sample-plan.md`, `sample-html-report.html`, etc.). They are target output, not part of any runnable app — don't treat them as source or config.

## Per-stack commands

Run these from inside the specific starter directory. There is **no root-level build or test runner** — each starter is standalone.

### Laravel (`starter-repos/laravel-starter/`) — PHP 8.2, Laravel 11
```bash
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate
php artisan serve                 # http://localhost:8000
php artisan test                  # full suite (PHPUnit)
php artisan test --filter TaskTest # single test class/method
./vendor/bin/pint                 # lint/format (Laravel Pint)
php artisan queue:work            # background worker (Track 2)
```

### FastAPI (`starter-repos/python-fastapi-starter/`) — Python 3.10+
```bash
python -m venv .venv && source .venv/bin/activate
pip install -r requirements.txt
cp .env.example .env
uvicorn app.main:app --reload     # http://localhost:8000 ; Swagger at /docs
pytest                            # full suite
pytest tests/test_tasks.py::test_name  # single test
```
`.venv/` is committed in this repo — it may be stale; recreate it if imports fail.

### Next.js (`starter-repos/nextjs-starter/`) — Next 15 App Router, TS, Node
```bash
npm install                       # postinstall runs `prisma generate`
cp .env.example .env
npx prisma migrate dev --name init
npm run dev                       # http://localhost:3000
npm test                          # Jest (ts-jest)
npx jest __tests__/tasks.test.ts  # single test file
npm run lint                      # ESLint (next lint)
```

## Architecture (big picture, shared across starters)

Every starter follows the same three-layer shape; recognize the analog when moving between stacks:

- **Auth is bespoke in each stack** — this is the single most important thing to know before extending auth.
  - Laravel: session-based `AuthController` + `guest`/`auth` route middleware groups (`routes/web.php`). No Breeze/Jetstream.
  - FastAPI: JWT bearer tokens (`app/auth.py`). `get_current_user` is the dependency you inject to protect a route; login issues a token via OAuth2 password flow at `/auth/login`.
  - Next.js: Auth.js v5 credentials provider with **JWT session strategy** (`auth.ts`). The user id is threaded through the `jwt`/`session` callbacks onto `session.user.id`.
- **Ownership scoping is the security model.** Every read/write is constrained to the current user: Laravel's `TaskController::authorizeTask()` aborts 403 on mismatch; FastAPI/Next.js filter queries by the authenticated user id. The assignments require this pattern to extend to new entities ("I can only mutate my own boards") — replicate it, don't bypass it.
- **Data access:** Laravel Eloquent (`app/Models`, migrations in `database/migrations`) · FastAPI SQLModel (`app/models.py`, tables created on startup via `init_db()`) · Next.js Prisma (`prisma/schema.prisma`, client singleton in `lib/prisma.ts`). All default to SQLite.
- **Request handling:** Laravel controllers + Blade views · FastAPI routers (`app/auth.py`, `app/tasks.py`) mounted in `app/main.py` · Next.js App Router route handlers under `app/api/**` plus server/client components under `app/`.

## Non-obvious conventions & gotchas

- **`Task.status` casing differs across stacks.** Laravel and FastAPI use lowercase `todo | doing | done`; Next.js uses uppercase `TODO | DOING | DONE` (see `lib/status.ts`). Match the stack you're in — cross-referencing the wrong casing silently breaks validation and filtering.
- **Tests use throwaway isolated databases**, not your dev SQLite file: Laravel runs against `:memory:` (`phpunit.xml`); FastAPI overrides the `get_session` dependency with an in-memory `StaticPool` engine (`tests/conftest.py`); the Next.js suite currently exercises only pure helpers (`lib/status.ts`) and does not touch the DB. When adding DB-backed tests to Next.js, wire up an isolated test database yourself.
- **Tailwind delivery differs:** Laravel loads Tailwind via CDN in `resources/views/layouts/app.blade.php` (no npm build); Next.js compiles it through PostCSS. Don't add an npm build step to the Laravel starter.
- **Laravel `QUEUE_CONNECTION=sync` by default.** Track 2 needs a real background worker — switch the queue driver (e.g. `database`) before relying on `queue:work`, or events run synchronously and the async behavior the rubric checks for won't appear.
- **Assignment CLAUDE.md deliverables are separate files.** The workflow has students commit their own project/user CLAUDE.md under `docs/claude-md-manual/` and `docs/claude-md-init/` inside their working copy. That is a graded artifact — do not conflate it with this repo-root CLAUDE.md.
- **TDD is graded on evidence, not just passing tests.** The rubric looks for a failing-test commit *before* the implementation commit (`test: …` then `feat: …`). If asked to help with an assignment, write and run the failing test first; do not generate test + implementation together.
- **This repo is not a git repository** and has no root README. Each starter is committed as plain files (Next.js `node_modules/` and FastAPI `.venv/` are present in-tree).
