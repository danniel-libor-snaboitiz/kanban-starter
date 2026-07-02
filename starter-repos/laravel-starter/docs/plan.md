# Implementation Plan — Kanban Board with Comments & @Mention Notifications

Feature domain added on top of the Laravel `Task` starter: **Board → Column → Card → Comment → Notification**, all scoped to one owning user. This plan is ordered by dependency and weaves the three graded TDD targets (mention parsing, notification creation, ownership authorization) into explicit test-first sub-actions so the git history shows a `test:` commit before each `feat:` commit.

> Produced by the Plan subagent (Step 3), then saved here. Time estimates target the ~90-minute TDD implementation budget; setup/design/plan/architecture/report are separate phases.

## Design decisions & conventions (read before coding)

- **Add a `username` column to `users`.** The brief mentions `@alice` tokens and `/users/{username}` links, but the starter only has `name`/`email`. Mentions need a space-free handle to resolve against, so add `username` (unique, nullable-safe backfill), extend the registration form + `UserFactory`, and seed a second user `alice`. Without this, mention parsing has nothing sound to resolve against.
- **Build an explicit `notifications` domain table, not Laravel's `Notifiable`/`notifications` table.** The manual CLAUDE.md calls for a real domain model + `/notifications` feed. Columns: `user_id` (recipient), `actor_id` (author), `card_id`, `type` (lowercase enum, e.g. `mention` — matches the `todo|doing|done` casing convention), `read_at` (nullable). Document this choice in the model docblock.
- **Ownership traverses relationships.** A `Card` is owned via `column → board → user_id`. Every `authorize*()` guard mirrors `TaskController::authorizeTask()` and `abort(403)` when the resolved owning board's `user_id !== Auth::id()`.
- **Follow existing Blade conventions.** Keep `@extends('layouts.app')` / `@section`; inject the nav unread badge via a shared view composer (new), not a slot-based layout rewrite. Tailwind stays CDN-loaded; no npm build.
- **Tooling per CLAUDE.md:** use `php artisan make:*` generators, add a factory with every model, tests use `RefreshDatabase` against in-memory SQLite, keep `tests/Unit/` populated, and run `vendor/bin/pint --dirty --format agent` before finalizing.

---

## Step 1 — Data-model foundation: migrations, models, factories, username + seeder
Create migrations, Eloquent models, and factories for `Board`, `Column`, `Card`, `Comment`, `Notification`; add the `username` column to `users` (unique) and update `UserFactory` + registration validation. Wire relationships (`Board hasMany Column`, `Column hasMany Card`, `Card hasMany Comment`, `User hasMany Board/Notification`) and a small seeder that creates a demo user plus `alice`. Use `foreignId()->constrained()->cascadeOnDelete()` like the tasks migration and keep any enum-ish field lowercase.
- **Time:** 10 min
- **Depends on:** none (foundation).

## Step 2 — Routes + controller skeletons (ownership-scoped happy path)
Add `BoardController`, `ColumnController`, `CardController`, `CommentController`, `NotificationController`, and a placeholder `UserController@show`; register all brief routes inside the existing `auth` middleware group (use `Route::resource` for boards, explicit routes for the nested/shallow ones). Implement the happy-path read/create/update/delete using ownership-scoped relationships (`Auth::user()->boards()`, `$column->cards()->create(...)`), and seed three columns (`Todo`/`Doing`/`Done`) on board create — but do **not** add authorization guards yet (that is Step 3's failing test).
- **Time:** 6 min
- **Depends on:** Step 1.

## Step 3 — Ownership authorization (TDD target #3)
Guard every board/column/card/comment mutation and read against the current user, mirroring `authorizeTask()`.
- **Test-first sub-action:** write failing feature tests in `tests/Feature/` where user B requests user A's board/column/card routes and asserts `403`; run them and confirm they fail (skeleton currently returns 200). Commit as `test: ownership authorization returns 403 across boards/columns/cards`.
- **Implement sub-action:** add `authorizeBoard/Column/Card/Comment()` guards that traverse to the owning board's `user_id` and `abort(403)`; make the tests green. Commit as `feat: ownership authorization guards for kanban resources`.
- **Time:** 12 min
- **Depends on:** Steps 1, 2. **This is TDD target #3.**

## Step 4 — Board & column views and CRUD wrap-up
Build `boards.index`, `boards.create`, `boards.show`, and the `boards._column` partial: list the user's boards, the new-board form, and the board view rendering three columns with card counts, plus add-column / rename-board / delete-board / rename-column / delete-column actions.
- **Time:** 10 min
- **Depends on:** Steps 2, 3.

## Step 5 — Cards + drag-to-move persistence  ⚠️ RISKIEST STEP
Build the `boards._card` draggable tile and card create/edit/delete, then implement moving a card between columns via `PATCH /cards/{card}` updating `column_id`, persisted server-side.
- **RISK:** browser drag/drop is the hardest-to-verify and most brittle part — HTML5 `dragstart`/`drop` wiring, CSRF on the async request, and the graded criterion is *server persistence*, not animation.
- **Mitigation:** keep the JS minimal (native `dragstart`/`drop` → `fetch` PATCH with the CSRF token from the existing `<meta name="csrf-token">`), and make the endpoint the source of truth. Add a **no-JS fallback** (a "Move to column" `<select>` or the card edit form also sets `column_id`) and cover the PATCH move with a **feature test** independent of the JS, so grading passes even if the drag UX is rough. Validate that `column_id` belongs to the same board (and is owned) to avoid cross-board moves.
- **Time:** 14 min
- **Depends on:** Step 4.

## Step 6 — MentionParser (TDD target #1)
Create a pure `MentionParser` helper that extracts `@username` tokens via regex and resolves them to existing `User`s.
- **Test-first sub-action:** write failing unit tests in `tests/Unit/` covering the edge cases — dedupe repeated mentions, case-insensitive lookup, punctuation boundaries (`@alice,`), no false match inside emails (`x@alice.com`), unknown handles resolve to nothing (no error), and self-mention. Commit as `test: mention parser extracts and resolves @username tokens`.
- **Implement sub-action:** implement the regex + `User::whereIn('username', …)` lookup returning matched users only; make tests green. Commit as `feat: MentionParser regex extraction and user resolution`.
- **Time:** 10 min
- **Depends on:** Step 1. **This is TDD target #1.** (Secondary risk area: the regex edge cases above are the subtle part — the exhaustive test list is the mitigation.)

## Step 7 — Comments + mention notifications (TDD target #2)
Persist comments and fan out notifications to mentioned users.
- **Test-first sub-action:** write a failing feature test that posting a comment containing `@alice` creates exactly one `Notification` row for `alice` (`type=mention`, `read_at=null`, linked to the card) and that an unknown handle creates zero rows and does not error. Commit as `test: posting a comment with @mention creates notifications`.
- **Implement sub-action:** implement `CommentController@store` (validate body, `$card->comments()->create(...)`), then a `NotificationService` that runs `MentionParser` over the body and creates one `Notification` per resolved user; render `cards.show` with the comment thread and `@username` → `/users/{username}` links. Make tests green. Commit as `feat: comment posting with @mention notification fan-out`.
- **Time:** 14 min
- **Depends on:** Steps 5, 6. **This is TDD target #2.**

## Step 8 — Notifications feed, unread badge, mark-read, profile placeholder
Build `notifications.index` (unread first, then earlier), the unread-count badge in the nav via a **shared view composer** (computes `$unreadCount` for the authed user on every page), `PATCH /notifications/{notification}` to set `read_at=now()` and redirect to the source card, and the placeholder `users.show` so mention links resolve.
- **Time:** 10 min
- **Depends on:** Step 7.

## Step 9 — Full-suite verification + formatting
Run the entire suite (`php artisan test --compact`) to confirm all TDD targets plus the starter `TaskTest` stay green, then run `vendor/bin/pint --dirty --format agent`. Confirm the git log shows the three `test:`-before-`feat:` pairs.
- **Time:** 4 min
- **Depends on:** all previous steps.

---

## Budget sanity check

| Step | Minutes |
| --- | --- |
| 1 Foundation | 10 |
| 2 Routes + skeletons | 6 |
| 3 Ownership authz (TDD #3) | 12 |
| 4 Board/column views | 10 |
| 5 Cards + drag persistence (RISK) | 14 |
| 6 MentionParser (TDD #1) | 10 |
| 7 Comments + notifications (TDD #2) | 14 |
| 8 Notifications feed + badge | 10 |
| 9 Verify + pint | 4 |
| **Total** | **90 min** |

Total lands on the ~90-minute TDD implementation budget. If time runs short, the compressible slack is in Step 5's drag/drop polish — the PATCH-move endpoint + its feature test (the graded persistence criterion) must stay; the animation is expendable.
