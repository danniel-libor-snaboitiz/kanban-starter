# User CLAUDE.md
_Global preferences that apply to every project I work on with Claude Code._

## About me

Senior engineer. Distributed systems and platform work. I ship less code than I once did — I care about correctness, observability, and blast radius. Assume I know the language and the framework; skip the tutorial tone.

## Language / stack preferences

- TypeScript with strict mode when doing frontend or Node.
- Go for services when latency and memory are constrained.
- Python for data pipelines, always with type hints and Pyright in strict mode.
- Avoid Ruby, Java, and Kotlin unless the existing repo forces it.

## Style

- Small, focused modules over big frameworks.
- No premature abstraction. Second time I see a pattern I refactor; not the first.
- Reject "clever" one-liners in favor of a boring loop. If a comprehension needs a comment to be read, it should be a loop.
- Named types over inline structural types once a shape appears more than twice.

## Testing philosophy

- Test the module boundary, not the internals.
- TDD for anything with branching logic — matchers, parsers, state machines, retry ladders.
- Never mock what I own; mock only third-party surfaces.
- Ask for a coverage report on any module that grows past ~150 LOC.
- If a test needs more than three lines of setup, the module boundary is probably wrong. Say so.

## Workflow preferences

- Multi-file edits: show the plan first, then diffs, then wait for my approval.
- Any change touching auth, billing, or a persistence boundary: pause and ask before proceeding, even if the change looks trivial.
- When using a subagent, name it in the commit message so future me can audit which parts of the codebase were written under which agent's supervision.
- Prefer deleting code to refactoring it. Prefer refactoring it to leaving it.

## Git

- Conventional commits (`feat`, `fix`, `test`, `refactor`, `chore`, `docs`, `perf`).
- Never squash-merge without asking.
- Never rewrite history on a branch that has been pushed.
- Commit messages should describe the why. The diff already describes the what.

## Communication

- Cite line numbers when you reference code (e.g., `app/Rules/MatcherService.php:42`).
- If you're less than 80% confident in an answer, say so out loud rather than hedging inside a paragraph.
- Prefer "I don't know, let me check" to a confident wrong answer.
