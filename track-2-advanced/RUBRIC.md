# Rubric — Track 2: Advanced (Automation Rules Engine)

Total: 100 points; bonus up to +15.

| Category | Weight | Excellent | Adequate | Missing |
| --- | --- | --- | --- | --- |
| CLAUDE.md quality | 10 | Project CLAUDE.md captures rule tree shape, pure vs. side-effect boundaries, and worker run steps; reflection is substantive and cites concrete divergences | Present but generic; reflection reads as summary rather than critique | Missing, or unchanged `/init` output submitted verbatim |
| Design brief | 5 | Nested condition builder wireframe is convincing (nested groups visible, add-child affordances shown); two error states designed with copy | Present but incomplete; one wireframe or one error state missing | Missing |
| Plan + plan-review | 10 | Plan is dense, ordered by dependency, and lists risk per step; review critiques concretely (line-level suggestions, not "looks good"); student integrated critique or documented why they did not | Plan present, review shallow (< 5 concrete suggestions) | Review missing, or plan is a bulleted list without dependencies |
| Architecture + arch-review | 15 | Data model, event flow, worker choice justified against an alternative, retry policy specified numerically; review checks plan consistency step-by-step | Missing one of these components | Missing two or more, or architecture contradicts plan without note |
| Feature correctness | 20 | Matcher handles nested trees with depth guard; actions run in order and one failing action does not block others; webhook retries with exponential backoff on a worker; dry-run works and does not persist | Missing dry-run, or missing retry, or matcher handles only flat trees | Core matcher broken, or actions run in wrong order |
| TDD evidence + coverage | 15 | Failing-test commits present on all three targets; coverage report shows 85%+ on matcher and executor; retry test asserts both count and intervals | Some TDD, low or missing coverage report; retry test asserts count only | No TDD evidence in git log, or no coverage report |
| Subagent orchestration | 10 | At least two distinct subagents used; explicit review loop where one subagent reviewed another's output; artifacts committed | One subagent, no review loop | No subagent evidence in report or git log |
| Plugin usage | 5 | `superpowers` or `cavern` invoked and visibly shaped the architecture doc; evidence in the workflow report (screenshot or excerpt) | Plugin listed but influence on architecture unclear | No plugin used |
| HTML workflow report | 10 | All 8 sections present, screenshots included, coverage report linked | Sections thin or one missing | Report missing |

## Bonus (+15 max)

- Rule execution history log at `/rules/{id}/history`: +5
- Admin webhook delivery view at `/admin/webhooks`: +5
- Dry-run supports hand-typed event JSON: +5

## Grading Notes for Instructor

- The subagent orchestration category is the differentiator between this track and Track 1. If evidence is absent, cap this category at 3 points regardless of other quality signals.
- Deduct 5 pts if webhook retries block the request thread instead of running in a background worker. Look for `HTTP::retry` or equivalent being called from a controller.
- Deduct 5 pts if the condition tree is flattened (only supports a single AND at the top level, no nesting).
- Deduct 10 pts if any of the three TDD targets lacks a failing-test commit before the implementation commit. This is a hard rule; do not soften it because the student's final tests pass.
- If the retry test asserts only "was retried" without checking the interval schedule, treat the TDD category as Adequate rather than Excellent.
- Plugin evidence must show the plugin's output, not just a mention of the plugin's name. A screenshot of a slash command being invoked with the resulting output is the canonical form.
