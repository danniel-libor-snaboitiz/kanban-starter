# Design Brief — Kanban with Comments & @Mentions

Design sketch for the Track 1 feature, built on the Laravel starter (Blade +
Tailwind). Another engineer should be able to implement from this without asking
questions. The UI is intentionally modest; correctness of the flows is the point.

## Scope recap

Single-user boards. A signed-in user creates boards, each seeded with three
columns (Todo / Doing / Done), adds cards to columns, drags cards between
columns, comments on cards, and `@mentions` users. A mention creates a
notification the mentioned user sees at `/notifications`, with an unread count in
the nav on every page.

## Routes → views

| Method | Path | View | Purpose |
| --- | --- | --- | --- |
| GET | `/boards` | `boards.index` | List my boards |
| GET | `/boards/create` | `boards.create` | New-board form |
| POST | `/boards` | — | Create board (+ seed 3 columns) |
| GET | `/boards/{board}` | `boards.show` | Board view: columns + cards |
| PATCH | `/boards/{board}` | — | Rename board |
| DELETE | `/boards/{board}` | — | Delete board |
| POST | `/boards/{board}/columns` | — | Add column |
| PATCH | `/columns/{column}` | — | Rename / reorder column |
| DELETE | `/columns/{column}` | — | Delete column |
| POST | `/columns/{column}/cards` | — | Create card in column |
| GET | `/cards/{card}` | `cards.show` | Card detail + comments |
| PATCH | `/cards/{card}` | — | Edit card / move column (drag) |
| DELETE | `/cards/{card}` | — | Delete card |
| POST | `/cards/{card}/comments` | — | Post comment (parses @mentions) |
| GET | `/notifications` | `notifications.index` | Notifications feed |
| PATCH | `/notifications/{notification}` | — | Mark one read (on click) |

All routes sit inside the existing `auth` middleware group and are scoped to the
current user (see the ownership model in the architecture doc).

---

## Wireframe 1 — Board view (`/boards/{board}`)

```
+---------------------------------------------------------------------------+
| Kanban        Boards   Notifications (3)                     me ▾  Logout |  <- nav, unread badge
+---------------------------------------------------------------------------+
|                                                                           |
|  Sprint Board                                    [ Rename ] [ Delete ]     |
|  ------------------------------------------------------------------------  |
|                                                                           |
|  +--------------+   +--------------+   +--------------+   [ + Add column ] |
|  | Todo      3  |   | Doing     1  |   | Done      2  |                    |
|  +--------------+   +--------------+   +--------------+                    |
|  | ┌──────────┐ |   | ┌──────────┐ |   | ┌──────────┐ |                    |
|  | │ Design   │ |   | │ Build API│ |   | │ Setup    │ |                    |
|  | │ #12  @me │ |   | │ #15  @me │ |   | │ #3       │ |                    |
|  | └──────────┘ |   | └──────────┘ |   | └──────────┘ |                    |
|  | ┌──────────┐ |   |              |   | ┌──────────┐ |                    |
|  | │ Write... │ |   |  (drop here) |   | │ Migrate  │ |                    |
|  | └──────────┘ |   |              |   | └──────────┘ |                    |
|  | ┌──────────┐ |   |              |   |              |                    |
|  | │ Tests    │ |   |              |   |              |                    |
|  | └──────────┘ |   |              |   |              |                    |
|  | [+ Add card] |   | [+ Add card] |   | [+ Add card] |                    |
|  +--------------+   +--------------+   +--------------+                    |
+---------------------------------------------------------------------------+

Card tile: draggable. Click title -> card detail. Drag onto another column ->
PATCH /cards/{id} with new column_id (persisted server-side, not client-only).
```

## Wireframe 2 — Card detail + comments (`/cards/{card}`)

```
+---------------------------------------------------------------------------+
| Kanban        Boards   Notifications (3)                     me ▾  Logout |
+---------------------------------------------------------------------------+
|  < back to Sprint Board                                                    |
|                                                                           |
|  Card #12 — "Design the board view"            [ Edit ]   [ Delete ]       |
|  Column: Todo        Assignee: @me                                         |
|  ------------------------------------------------------------------------  |
|  Description                                                               |
|  Lay out the three columns and the card tiles...                          |
|  ------------------------------------------------------------------------  |
|  Comments (2)                                                              |
|                                                                           |
|   @alice · 2h ago                                                         |
|   Looks good. cc @me — can you take the API card?         <- @me is a link |
|                                                                           |
|   @me · 1h ago                                                            |
|   On it.                                                                   |
|  ------------------------------------------------------------------------  |
|  Add a comment                                                             |
|  +---------------------------------------------------------------+        |
|  | Type @ to mention someone...                                  |        |
|  +---------------------------------------------------------------+        |
|                                                      [ Post comment ]      |
+---------------------------------------------------------------------------+

@username tokens in a rendered comment become links to /users/{username}
(placeholder profile route is fine) and, on post, create a Notification row for
each mentioned, existing user.
```

## Wireframe 3 — Notifications feed (`/notifications`)

```
+---------------------------------------------------------------------------+
| Kanban        Boards   Notifications (3)                     me ▾  Logout |
+---------------------------------------------------------------------------+
|  Notifications                                          [ Mark all read ]  |
|  ------------------------------------------------------------------------  |
|  UNREAD                                                                    |
|  ● @alice mentioned you on "Design the board view"          2h ago         |
|  ● @alice mentioned you on "Build API"                      3h ago         |
|  ● @bob mentioned you on "Setup"                            1d ago         |
|  ------------------------------------------------------------------------  |
|  EARLIER                                                                   |
|    @alice mentioned you on "Migrate DB"                     3d ago  (read) |
+---------------------------------------------------------------------------+

Unread first, chronological. Clicking a row marks it read (PATCH) and navigates
to the source card. The nav badge count = unread notifications for current user.
```

---

## Component list (Blade views, partials & components)

Laravel uses Blade views and partials rather than JS components. "Props" = the
data passed into each view/partial.

| Component | Type | Props (data in) | Responsibility |
| --- | --- | --- | --- |
| `layouts.app` | layout | `$slot`, `$unreadCount` (via shared view composer) | Page shell, Tailwind CDN, mounts nav. |
| `layouts.nav` | partial | `$unreadCount` | Links (Boards, Notifications) + unread badge; shown on every page. |
| `boards.index` | view | `$boards` (user's boards) | List boards with links; "New board" button. |
| `boards.create` | view | — | Board name form → POST `/boards`. |
| `boards.show` | view | `$board` (with `columns.cards`) | Board title + actions; renders columns; hosts drag/drop JS. |
| `boards._column` | partial | `$column` (with `cards`) | One column: header + card count, its cards, add-card + rename/delete. |
| `boards._card` | partial | `$card` | Draggable tile: title, `#id`, assignee; links to card detail. |
| `cards.show` | view | `$card` (with `comments.user`, `column`) | Card metadata, description, comment thread, comment form. |
| `comments._comment` | partial | `$comment` | Render one comment; `@username` → profile links; author + timestamp. |
| `comments._form` | partial | `$card` | Textarea + submit → POST `/cards/{card}/comments`. |
| `notifications.index` | view | `$notifications` (unread first) | Feed grouped unread/earlier; each row marks read + links to card. |
| `users.show` | view | `$user` | Placeholder profile page for a mention target. |

Server-side helper (not a view):

| Unit | Responsibility |
| --- | --- |
| `MentionParser` | Extract `@username` tokens from comment body (regex), resolve to existing `User`s, return matched users. Pure + unit-tested (TDD target #1). |
| `NotificationService` (or model event) | On comment save, create a `Notification` per mentioned user (TDD target #2). |
| Ownership guards | `authorize*()` checks mirroring `TaskController::authorizeTask()` for Board/Column/Card/Comment (TDD target #3). |

---

## User flows

### Flow A — Create a card

1. User opens `/boards/{board}` (board view).
2. Clicks **[+ Add card]** at the bottom of a column (e.g. Todo).
3. Inline form (or small page) captures **title** (required) and optional
   description; submits **POST `/columns/{column}/cards`**.
4. Server validates, creates the card via the ownership-scoped relationship
   (`column->cards()->create(...)`), associates it with the column.
5. Redirect back to the board; the new tile appears at the bottom of that column.
6. **Acceptance:** the card exists in the DB under the right `column_id` and is
   visible after a page reload.

### Flow B — Add a comment that includes an @mention

1. User opens a card at `/cards/{card}`.
2. Types a comment in the form, e.g. `Nice work @alice — over to you`.
3. Submits **POST `/cards/{card}/comments`**.
4. Server saves the `Comment` (body + author + card).
5. `MentionParser` extracts `@alice`, looks up the `User` with username `alice`.
6. For each resolved, existing user, a `Notification` row is created
   (`type=mention`, links to this card, `read_at=null`).
7. Redirect back to the card; the comment renders with `@alice` as a link to
   `/users/alice`.
8. **Acceptance:** a notification row exists for `alice`; unknown handles create
   no rows and don't error.

### Flow C — Mentioned user sees and clicks a notification

1. `alice` signs in. On every page the nav shows **Notifications (1)** — her
   unread count (a shared view composer computes it).
2. She clicks **Notifications** → `/notifications`.
3. The feed lists her notifications, **unread first**; the mention row is bold
   with an ● marker.
4. She clicks the row → **PATCH `/notifications/{id}`** sets `read_at=now()` and
   redirects to the source card `/cards/{card}`.
5. Back on any page, the nav badge count has decreased by one.
6. **Acceptance:** clicking marks exactly that notification read and lands on the
   correct card; the unread badge reflects the change.

---

## Notes & non-goals

- One level of comments only — no nested replies.
- "Other users" in the beginner track = a seeded second user (e.g. `alice`) or a
  second browser session; no sharing/permissions between users' boards.
- Drag/drop can be minimal (HTML5 drag events or a tiny script) as long as the
  move **persists server-side** — that is the graded acceptance criterion, not
  the animation quality.
- Profile route `/users/{username}` is a placeholder; it only needs to exist so
  mention links resolve.
