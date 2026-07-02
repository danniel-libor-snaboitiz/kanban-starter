# CLAUDE.md — manual vs `/init` notes

How the hand-written files (`docs/claude-md-manual/`) differ from the generated
ones (`docs/claude-md-init/`), and what I changed and why.

## Project file: what `/init` did well

`/init` is genuinely good at the *inventory*. It correctly scanned and reported:

- the real installed versions (PHP 8.5, Laravel 12, Filament v5, PHPUnit 11) —
  notably newer than the course README implies;
- the directory layout, routes, models, and relationships;
- the in-memory-SQLite test setup from `phpunit.xml`;
- the exact existing test and the single `UserFactory`.

For a factual map of "what is in this repo right now," the generated file is more
complete than what I'd bother to type by hand, and I kept most of it.

## Project file: what `/init` missed or got wrong

The gap is *judgment*, not facts. `/init` documents what exists; it can't know
what matters or what I'm about to build. My manual file adds:

- **The "you'll get this wrong" framing.** `/init` lists `status: todo|doing|done`
  as a neutral fact. The manual file flags the lowercase casing as a *trap* that
  new models must follow — because the sibling Next.js starter uses UPPERCASE and
  it's an easy cross-stack mistake.
- **Intent.** `/init` describes a `Task` CRUD app. The manual file says the point
  is to build Board/Column/Card/Comment/Notification with `@mentions`, and that
  ownership scoping and the notification model are the load-bearing parts.
- **The `Notifiable` nuance.** `/init` reports the trait exists. The manual file
  turns that into a decision: the assignment wants an explicit `Notification`
  table + feed, so don't just lean on Laravel's built-in notifications table.
- **A quirk `/init` literally cannot see:** `tests/Unit/` must exist or PHPUnit
  aborts the entire run. That surfaced only by running the suite (it failed on a
  fresh checkout), not by scanning files — exactly the kind of hard-won fact a
  human adds and a scan omits.
- **Corrections.** `/init` first labeled Pint as "PSR-12" and leaned toward
  treating Filament as the UI layer; the assignment UI is plain Blade, so I
  trimmed that emphasis.

Net: I edited the generated file rather than shipping it as-is — deleting generic
lines, and adding the two or three things a fresh model would never guess. Raw
`/init` output submitted unchanged is a rubric penalty, and it deserves to be:
the value of the file is the judgment layered on top of the scan.

## User file: the honest difference

`/init` is project-scoped — it does **not** generate a user-level CLAUDE.md. The
"generated" user file is therefore the generic template Claude produces when asked
for one, left mostly unedited so the contrast is visible.

The template is symmetric and vague ("write clean code", "aim for good
coverage") — advice no one disagrees with and no one can act on. The manual
version is *opinionated and actionable*: be concise and skip preamble; write the
failing test, run it, see it fail, then implement (never both at once); comments
explain *why* not *what*; edit over create; confirm before anything irreversible.
Those are real constraints Claude can follow and I can check.

## Takeaway

`/init` is a fast, accurate first draft for the *project* file and a starting
point worth pruning — treat it as a scan to edit, not an answer to accept. It is
not a substitute for the *user* file, where the whole value is personal
preference the tool has no way to know. The 30 minutes of editing is where the
useful signal gets added.
