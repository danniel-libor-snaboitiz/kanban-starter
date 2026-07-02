# Submission Checklist — Track 2: Advanced

Complete every item before submission. If a step is skipped, note why in your reflection.

- [ ] Git repo initialized and committed after each phase
- [ ] User CLAUDE.md — manual + `/init` versions (both committed)
- [ ] Project CLAUDE.md — manual + `/init` versions (both committed)
- [ ] `docs/claude-md-notes.md` — reflection on differences between manual and `/init` output
- [ ] `design/design-brief.md` — wireframes, component tree, two error states
- [ ] `docs/plan.md` and `docs/plan-review.md` — both produced by subagents, review integrated or dissented from in writing
- [ ] `docs/architecture.md` and `docs/architecture-review.md` — architecture produced with `superpowers` or `cavern`, review checks plan consistency
- [ ] Rule matcher, action executor, and webhook retry all have a failing-test commit before their implementation commit
- [ ] All tests passing (`<test command for your stack>`; also run the worker if the stack requires it)
- [ ] Coverage report attached under `report/coverage/` (HTML or JSON)
- [ ] `report/workflow-report.html` — 8 sections including the orchestration diagram and plugin evidence
- [ ] Bonus items listed and completed (optional):
  - [ ] Rule execution history log at `/rules/{id}/history`
  - [ ] Admin webhook delivery view at `/admin/webhooks`
  - [ ] Dry-run supports hand-typed event JSON
- [ ] Push to remote and share URL
