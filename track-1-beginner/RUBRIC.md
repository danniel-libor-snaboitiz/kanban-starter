# Rubric — Track 1: Beginner (Kanban with Comments & @Mentions)

Total: **100 points**.

Grade against the artifacts committed to the repo. Where an artifact is missing, award zero for that row; do not infer effort from other artifacts.

| Category | Weight | Excellent (full) | Adequate (partial) | Missing (0) |
| --- | --- | --- | --- | --- |
| CLAUDE.md quality (manual + /init) | 15 | Both manual and `/init` versions present at both user and project levels; substantive project-specific content; short reflection meaningfully compares the two versions. | Files present but generic; reflection thin or mostly restates what each file contains. | One or both files missing, or a copy-paste of `/init` output with no edits. |
| Design brief | 10 | Wireframes cover all core flows (board, card detail, notifications); component list matches what was actually built; user flows are step-by-step. | Wireframes exist but incomplete, or component list drifts from the implementation. | No design brief. |
| Plan doc | 10 | 7+ numbered steps, dependencies noted, time estimates on each step, at least one risk flagged explicitly. | Steps present but shallow; no dependencies or estimates. | No plan, or a flat bullet list with no structure. |
| Architecture doc | 10 | Data model diagram + API table + state flow + two tradeoffs, all coherent with the code. | Missing one of the four components. | Missing two or more components. |
| Feature correctness | 20 | All acceptance criteria pass on demo; drag/drop works and persists; mentions link and generate notifications. | Most criteria pass; small gaps (e.g., reorder doesn't persist, mention regex misses edge cases). | Core flows broken or not runnable. |
| TDD evidence | 15 | Git log shows failing-test commits before implementation for all three TDD targets (mention parsing, notification creation, authorization). | Some TDD, some code-first commits. | No failing-test commits, or tests written after the fact in a single commit. |
| HTML workflow report | 15 | All six required sections present, screenshots included, clean inline CSS, self-contained. | Sections thin or missing screenshots, or relies on external assets. | Report missing or plain text only. |
| Reflection / polish | 5 | Specific, honest reflection that names a concrete surprise and a concrete change; code passes the project's linter. | Reflection is generic ("I learned a lot"). | No reflection. |

## Grading Notes for Instructor

- Award partial credit within a row. A row is not all-or-nothing — a "mostly excellent" entry can score, for example, 12/15.
- **Bonus 5 pts** for any student who successfully uses two different subagents in the plan or architecture phases (e.g., planning subagent for the plan, general-purpose subagent for architecture, or a plugin subagent for either).
- **Penalty 10 pts** if the `/init` output is submitted unchanged as the CLAUDE.md file. This indicates no engagement with the tool and defeats the purpose of the exercise.
- Feature correctness should be graded from a live demo, not from the report. If the student's environment does not run, note that in the feedback and grade the artifacts only.
- TDD evidence should be graded from the git log directly. Ask the student to run `git log --oneline` in the demo.
