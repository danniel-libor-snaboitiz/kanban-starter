# Project CLAUDE.md — Automation Rules Engine (Laravel 11)

## Stack

- Laravel 11, PHP 8.3
- Postgres 16 (dev + prod)
- Redis for queues (Horizon)
- Blade + Alpine for UI, plus Livewire for the rule builder tree
- PHPUnit + Pest for tests
- xdebug for coverage

## Layout

- `app/Models/` — `Task`, `Rule`, `RuleExecution`, `WebhookAttempt`
- `app/Rules/` — pure logic (no I/O, no clock reads, no framework helpers):
  - `MatcherService.php` — evaluates a condition tree against a `TaskChanged` event
  - `ActionRunner.php` — executes one action, returns a result record
  - `RuleTreeValidator.php` — validates the JSON tree shape and depth
- `app/Jobs/EvaluateRuleJob.php` — the queue job that ties matcher + runner together
- `app/Events/TaskChanged.php` — emitted from Task observers
- `app/Http/Livewire/RuleBuilder.php` — condition tree editor
- `tests/Unit/` — pure logic tests, high coverage
- `tests/Feature/` — HTTP + queue integration

## Rule tree shape

- Persisted as JSON in `rules.conditions`.
- Schema is validated by `RuleTreeValidator::validate($tree)` before save. Never trust the client's tree.
- Leaf: `{ "predicate": "status_equals" | "label_equals" | "title_contains" | "assigned_to_equals", "value": <string|number> }`.
- Composite: `{ "op": "AND" | "OR", "children": [tree, ...] }`.
- Max depth: 8. Trees deeper than this are rejected at validation time, not at evaluation time.

## Pure vs. side-effect boundary

Pure (TDD, unit tests, high coverage):

- `MatcherService::matches($rule, $event)`
- `RuleTreeValidator::validate($tree)`
- The retry-schedule calculator in `ActionRunner`.

Side-effectful (integration tests, do not unit-mock):

- `EvaluateRuleJob::handle()` — orchestrates matcher + runner.
- The HTTP client that talks to webhook URLs.
- Livewire components that read from and write to the database.

## Commands

- Run app: `php artisan serve`
- Run worker: `php artisan queue:work --queue=rules`
- Fresh DB: `php artisan migrate:fresh --seed`
- All tests + coverage: `php artisan test --coverage --min=85`
- Only pure-logic tests: `php artisan test tests/Unit/`

## What to be careful about

- Webhook retries must be off the request thread. Never call the HTTP client from a controller.
- The condition tree can be nested. Depth-first evaluation with short-circuiting is fine; recursion is fine up to depth 8.
- The Livewire rule builder is stateful; keep it thin and let `RuleTreeValidator` remain the single source of truth for what a valid tree looks like.
- Never trust `action.type` or `action.status` coming from the client — whitelist against the enum before persisting.
- `RuleExecution` rows are written even for skipped runs (matcher returned false). Grep for "audit" before removing that.

## Where to look when you're stuck

- `tests/Unit/MatcherTest.php` — canonical examples of matcher tests and the tree fixtures used throughout the suite.
- `app/Rules/ActionRunner.php` — model for how side-effectful actions record their outcome without swallowing exceptions.
