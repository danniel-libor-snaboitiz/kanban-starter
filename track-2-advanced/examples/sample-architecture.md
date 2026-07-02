# Architecture — Automation Rules Engine

## Data model

```mermaid
erDiagram
    USER ||--o{ TASK : owns
    USER ||--o{ RULE : creates
    RULE ||--o{ RULE_EXECUTION : produced
    RULE_EXECUTION ||--o{ WEBHOOK_ATTEMPT : caused
    TASK ||--o{ RULE_EXECUTION : matched_by

    USER { id name email }
    TASK { id user_id title description status assignee_id }
    LABEL { id name }
    TASK_LABEL { task_id label_id }
    RULE { id created_by name event conditions_json actions_json is_active last_fired_at }
    RULE_EXECUTION { id rule_id task_id matched_at result_json duration_ms }
    WEBHOOK_ATTEMPT { id rule_execution_id url status_code attempt_no latency_ms error }
```

## Event flow

```
Task mutation
  |
  v
TaskObserver::updated -> event TaskChanged { pre, post }
  |
  v
CandidateRuleSelector picks Rules where event_type matches
  |
  v
EvaluateRuleJob dispatched onto queue "rules" (Redis)
  |
  v
Worker (php artisan queue:work --queue=rules)
  |
  +--> MatcherService::matches(Rule, event)   --> false: log skip row, done
  |
  +--> ActionRunner::run(action_i)            --> RuleExecution row
             |
             +--> if action is notify_webhook: WebhookClient with retry ladder
                     - attempt 1 (immediate)
                     - attempt 2 (after 1s + jitter)
                     - attempt 3 (after 4s + jitter)
                     each attempt writes a WebhookAttempt row
```

## API surface

| Method | Path | Purpose |
| --- | --- | --- |
| GET | `/rules` | List rules for the current user |
| POST | `/rules` | Create a rule (validates the tree via `RuleTreeValidator`) |
| GET | `/rules/{rule}` | Show a rule |
| PATCH | `/rules/{rule}` | Update a rule |
| DELETE | `/rules/{rule}` | Delete a rule |
| POST | `/rules/{rule}/dry-run` | Evaluate the rule against a Task or hand-typed event; return match result and would-fire actions |
| GET | `/rules/{rule}/history` | List `RuleExecution` rows for the rule (bonus) |
| GET | `/admin/webhooks` | Cross-rule webhook attempt list (bonus, admin only) |

## Worker choice

Postgres + Redis + Laravel Horizon. Horizon is the default queue supervisor for Laravel; it adds no new infrastructure beyond the Redis instance we already need for cache, and it ships with a supervisor UI that we get for free.

The alternative considered was a database-backed queue driver, which would have removed the Redis dependency. Rejected because it introduces lock contention on the jobs table under load — the whole point of the rules engine is that a burst of task mutations produces a burst of jobs, and a database queue is the wrong shape for that traffic pattern. The Redis dependency is worth the cost.

## Retry policy

Exponential ladder with jitter to avoid thundering-herd retries against a struggling webhook target.

- Attempt 1: immediate.
- Attempt 2: 1s ± 250ms.
- Attempt 3: 4s ± 1s.
- Cap: 3 attempts total.

Every attempt writes a `WebhookAttempt` row regardless of outcome. After the third failure, the parent `RuleExecution` row is marked `partial_failure` and a Sentry breadcrumb is emitted; the breadcrumb is not blocking and the worker moves on to the next queued job.

## Tradeoffs considered

### Nested condition trees vs. flat AND/OR

Chose nested. A flat DNF (disjunctive normal form) representation is more compact for evaluation and simpler to test, but painful to display in the builder UI. The user story "if `(status=done AND assignee=me) OR (label=urgent AND status!=archived)`" needs true nesting, and forcing users to think in DNF would produce a builder they cannot use.

### Synchronous vs. queued evaluation

Chose queued. A rule with a slow webhook must not block the mutation that triggered it. The cost is complexity: the worker needs to be running, and the eventual-consistency window (typically <2s) has to be explained in the UI. The cost is smaller than the cost of a webhook target taking 30s to respond and holding the request thread hostage.

### Recording every webhook attempt vs. only failures

Chose every attempt. Storage is cheap; operational visibility during on-call is not. Row shape is kept small — no request or response bodies, only URL, status code, attempt number, latency, and error message.

## Rejected alternatives

- Cron-driven polling — trades queue infrastructure for latency. The product feels dead if rules fire minutes late; rejected.
- Executing arbitrary JavaScript in the actions — rejected on security grounds. Users get a curated action list. Extending the action list requires a code change, and that is the point.

## Consistency with the plan

Verified against `docs/plan.md`: every plan step maps to a section here. The review agent's cross-check is in `docs/architecture-review.md`.
