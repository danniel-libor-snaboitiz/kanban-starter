# Next.js Starter

A minimal Next.js 15 + Auth.js v5 + Prisma + SQLite + Tailwind starter for training.

## Setup

```bash
npm install
cp .env.example .env
npx prisma migrate dev --name init
npm run dev
```

Then open http://localhost:3000.

## Usage

- Register a new user at `/register`
- Log in at `/login` (or use the Auth.js route at `/api/auth/signin`)
- View and create your tasks at `/tasks`

## Tests

```bash
npm test
```

## Stack

- Next.js 15 (App Router) + TypeScript
- Auth.js v5 (next-auth beta) with credentials provider (email + password, bcrypt)
- Prisma ORM with SQLite
- Tailwind CSS
- Jest for unit tests

## Data model

One CRUD entity `Task` (id, userId, title, description, status, createdAt, updatedAt) scoped to the current user. Status is a `String` with allowed values `TODO | DOING | DONE` (SQLite enums are avoided for portability).
