# User CLAUDE.md

_Global preferences that apply to every project I work on with Claude Code._

## About me

I'm a full-stack engineer. I care about clarity over cleverness and prefer boring, well-tested code.

## Language / stack preferences

- TypeScript over JavaScript when I have a choice.
- Python 3.11+ with type hints on function signatures.
- Prefer standard library and small, well-maintained deps.

## Style

- 2-space indent for JS/TS, 4-space for Python.
- Single quotes in JS/TS unless a project's Prettier config says otherwise.
- Comments should explain WHY, not WHAT.

## Testing philosophy

- Prefer TDD when the shape of the API is unclear.
- Write one failing test, then the smallest thing that makes it pass, then refactor.
- Never mark a task done if tests aren't green.

## Workflow preferences

- Before applying multi-file edits, show me the diff and wait for confirmation.
- Run the test suite after any feature-level change.
- If you're about to install a new dep, tell me why and give the smallest-footprint option.

## Git

- Small, imperative commits (`feat:`, `test:`, `fix:`, `refactor:`, `docs:`).
- Never `git push --force` without asking me.

## Communication

- Be direct; skip preamble.
- If I ask a question I already answered upthread, remind me instead of re-explaining.
