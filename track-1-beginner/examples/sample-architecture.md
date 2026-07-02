# Architecture — Kanban with Comments & @Mentions

## Data model

```mermaid
erDiagram
    USER ||--o{ BOARD : owns
    BOARD ||--o{ COLUMN : has
    COLUMN ||--o{ CARD : contains
    CARD ||--o{ COMMENT : has
    USER ||--o{ COMMENT : writes
    USER ||--o{ NOTIFICATION : receives
    COMMENT ||--o{ NOTIFICATION : triggers

    USER { id name email }
    BOARD { id user_id name }
    COLUMN { id board_id name position }
    CARD { id column_id title description assignee_id position }
    COMMENT { id card_id user_id body created_at }
    NOTIFICATION { id user_id type data read_at created_at }
```

## API endpoints

| Method | Path | Auth | Purpose |
| --- | --- | --- | --- |
| GET | `/boards` | required | List my boards |
| POST | `/boards` | required | Create board |
| GET | `/boards/{board}` | owner | Show board with columns and cards |
| PATCH | `/boards/{board}` | owner | Rename board |
| DELETE | `/boards/{board}` | owner | Delete board |
| POST | `/boards/{board}/columns` | owner | Add column |
| PATCH | `/columns/reorder` | owner | Bulk reorder |
| POST | `/columns/{column}/cards` | owner | Add card |
| PATCH | `/cards/{card}/move` | owner | Move card between columns |
| POST | `/cards/{card}/comments` | any signed-in user with board access | Post comment |
| GET | `/notifications` | required | List my notifications |
| PATCH | `/notifications/{n}/read` | owner | Mark read |

## State flow: comment with @mention -> notification

1. User posts a comment. `CommentController@store` validates and persists.
2. `MentionParser::parse(body)` extracts `@usernames` and resolves each to a `User` record (or `null` if unknown).
3. For each resolved user, a `Notification` row is inserted with `type = mention` and `data = { comment_id, card_id, from_user }`.
4. Notifications middleware includes an unread count on every page render.
5. When the recipient loads `/notifications` and clicks an item, `PATCH /notifications/{n}/read` sets `read_at = now()`.

## Tradeoffs considered

### Polling vs. websockets

Chose polling (a nav-badge query on every page navigation). Websockets add infra (Reverb/Pusher) that isn't in the beginner scope. If a user has more than 500 unread notifications the badge query would slow down; the index on `(user_id, read_at)` keeps it under 5ms for realistic volumes.

### Storing mentions on the Comment vs. deriving on read

Chose to persist Notification rows at write time rather than re-parsing at read time. Persisted rows let us track `read_at` per recipient, and they mean the feed query is a single indexed lookup instead of a scan across every comment.

## Rejected alternatives

- **Realtime drag-drop via broadcast** — cut. Users on separate devices reload to see each other's changes.
- **Nested comments beyond one level** — cut. Threads add UI complexity that doesn't test what this assignment is testing.
