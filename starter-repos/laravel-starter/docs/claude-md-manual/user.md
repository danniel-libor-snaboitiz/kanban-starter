# User CLAUDE.md (manual)

Global preferences that apply to every project I work on with Claude Code.
This is the hand-written version — see `docs/claude-md-init/user.md` for the
generated counterpart and `docs/claude-md-notes.md` for the comparison.

## How to communicate with me

- Be concise. Lead with the answer, then the reasoning. Skip preamble like
  "Great question!" and end-of-turn filler.
- When you change code, tell me *what* changed and *why* in one or two lines.
  I don't need a restatement of the diff.
- If you're unsure or made an assumption, say so explicitly rather than
  presenting a guess as fact.
- Surface tradeoffs when a decision is non-obvious; give me a recommendation,
  not a menu.

## How I like to work

- **Test-first.** For any non-trivial logic, write a failing test, run it, see
  it fail, *then* implement. Do not write the test and the implementation in the
  same step. Do not delete or weaken a test to make it pass.
- **Small, reviewable steps.** Prefer a sequence of focused commits over one
  large change. Commit after each logical unit.
- **Match the surrounding code.** Follow the naming, structure, and idioms of the
  files you're editing. Check sibling files before inventing a new pattern.
- **Edit over create.** Prefer changing existing files to adding new ones. Never
  create docs, READMEs, or scripts unless I ask.

## Code style

- Comments are for *why*, not *what*. Keep them sparse; let clear names carry the
  intent. Match the comment density already in the file.
- Descriptive names over clever ones (`isRegisteredForDiscounts`, not `chk()`).
- Use the language's and framework's own idioms and generators rather than
  hand-rolling equivalents.
- Run the project's formatter/linter before you consider a change done.

## Testing philosophy

- Cover the happy path, the failure path, and the obvious edge cases.
- Tests must be isolated and repeatable — no shared mutable state, no reliance on
  a dev database or on test ordering.
- Run the narrowest relevant test while iterating; run the full suite before I
  call something finished.

## Safety and git

- Confirm before anything hard to reverse: deleting files, force-pushing,
  rewriting history, dropping data.
- Never commit secrets. `.env` stays out of git; only `.env.example` is tracked.
- Don't push or open PRs unless I ask.
