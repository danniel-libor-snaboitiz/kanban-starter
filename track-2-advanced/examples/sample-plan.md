# Implementation Plan — Automation Rules Engine

_Produced by the planning subagent, then revised after a critique from a review subagent (see `docs/plan-review.md`)._

## Assumptions

- Starting from `starter-repos/laravel-starter/` on branch `main`.
- Postgres + Redis available locally; Docker Compose provided in the starter.
- Coverage target: 85%+ on the matcher and executor modules.
- Horizon is used as the queue supervisor. Alternatives considered in `docs/architecture.md`.

## Steps

### 1. Data layer — Rule, RuleExecution, WebhookAttempt (40 min)

- Migrations with JSON columns for `conditions` and `actions`; indexed columns for `event`, `is_active`, and `created_by`.
- `RuleTreeValidator` service (pure) with tests written before implementation.
- **TDD.** At least 8 tests: leaf-only, nested AND, nested OR, mixed nesting, depth-guard rejection, invalid predicate, invalid op, non-object leaf.
- **Depends on:** nothing.
- **Risk:** low.

### 2. Event bus — TaskChanged event + observer (30 min)

- `TaskObserver` fires `TaskChanged` on status, label, or assignment changes.
- Event payload holds `pre` and `post` task snapshots so downstream code can compute the delta.
- Feature test: mutating a Task emits the event exactly once.
- **Depends on:** starter `Task` model.
- **Risk:** low.

### 3. Matcher service (60 min)

- `MatcherService::matches(Rule $rule, TaskChanged $event): bool` — recursive descent with short-circuiting.
- **TDD required.** 10+ cases including nested AND/OR, mixed nesting, depth-guard, event-type mismatch, and empty children.
- **Risk:** medium. Nested trees are where bugs live; short-circuiting must not skip validation.
- **Depends on:** 1, 2.

### 4. Action runner — set_status, add_label, in-app notification (45 min)

- One method per action type; each records a `RuleExecution` row with per-action outcome.
- **TDD.** Test that a failure in one action does not roll back or block preceding or subsequent actions.
- **Risk:** low-medium — exception boundaries need care.
- **Depends on:** 3.

### 5. Webhook action + retry ladder (60 min)

- `notify_webhook` uses the Laravel `Http` client.
- Exponential backoff schedule: 1s, 4s, 16s. Cap 3 attempts total.
- Every attempt is recorded in `webhook_attempts` (URL, attempt number, status code, latency, error).
- **TDD.** Use `Http::fake` to assert the retry schedule — both the attempt count and the intervals. See reviewer's note below.
- **Risk:** medium — mocking backoff timers cleanly is fiddly and worth extra care.
- **Depends on:** 4.

### 6. Queue job — EvaluateRuleJob (30 min)

- Job takes an event plus the set of candidate rules; runs matcher; if matched, runs the action runner.
- Registered on the `rules` queue.
- Integration test: fire event, run worker inline, assert the expected side effects.
- **Depends on:** 3, 4, 5.

### 7. Rule builder UI (Livewire) (60 min)

- Rule list page with active toggle, name, event, last-fired timestamp.
- Editor with nested condition tree — add-group and add-leaf buttons at every level, and a delete affordance for both.
- Dry-run panel that calls `MatcherService::matches` against a synthetic event derived from a chosen Task.
- **Depends on:** 3.

### 8. Bonus (optional, up to 45 min)

- 8a. Rule execution history page at `/rules/{id}/history`.
- 8b. Admin `/admin/webhooks` list of delivery attempts across all rules.
- 8c. Dry-run panel accepts hand-typed event JSON in addition to picking from existing tasks.

### 9. HTML workflow report + coverage export (30 min)

- Run `php artisan test --coverage-html report/coverage/`.
- Fill in all 8 sections including the orchestration diagram and plugin evidence.

## Total

~6 hours end-to-end. The bonus block is genuinely optional — do not sink the base plan for it. If time is short, skip 8a-c before you skip test coverage.

## Notes from review

- The reviewer flagged step 5's retry test as easy to get wrong: assert both the attempt count AND the intervals, not just "was retried." Reflected in the step-5 TDD line above.
- The reviewer suggested moving step 6 earlier so the worker exists before it has to be consumed by the UI's dry-run. I disagreed: dry-run bypasses the queue by design, so the worker is not on the critical path for step 7. Left as-is.
- The reviewer flagged step 1's migration order (rules before task changes) as fine.
