# Submission Checklist — Track 1: Beginner

Complete every item before submission. If a step is skipped, note why in your reflection.

- [ ] Git repo initialized and committed after each phase (setup, CLAUDE.md, design, plan, architecture, implementation, report)
- [ ] User CLAUDE.md — manual version + `/init` version (both committed under `docs/claude-md-manual/user.md` and `docs/claude-md-init/user.md`)
- [ ] Project CLAUDE.md — manual version + `/init` version (both committed under `docs/claude-md-manual/project.md` and `docs/claude-md-init/project.md`)
- [ ] `docs/claude-md-notes.md` — short reflection on how the manual and `/init` versions differ
- [ ] `design/design-brief.md` — wireframes + component list + user flows
- [ ] `docs/plan.md` — numbered plan produced by a planning subagent
- [ ] `docs/architecture.md` — data model + API table + state flow + tradeoffs
- [ ] Feature implemented, all tests passing:
  - Laravel: `php artisan test`
  - FastAPI: `pytest`
  - Next.js: `npm test`
- [ ] Git log shows tests-first commits for: mention parsing, notification creation, board/column/card authorization
- [ ] `report/workflow-report.html` — self-contained, all six required sections
- [ ] Push to remote and share the repo URL
