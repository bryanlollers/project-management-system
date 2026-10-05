# Project Management System

A full-stack portfolio project built with Laravel and Nuxt 3 for managing clients, projects, tasks, and team reporting. It demonstrates REST API design, role-based authorization, feature-based architecture, automated testing, and a Docker development workflow.

- **Backend:** Laravel 12, PHP 8.4, PostgreSQL 17 (Supabase for deployment), Sanctum, Eloquent, Form Requests, API Resources, and policies.
- **Frontend:** Nuxt 3, Vue 3, TypeScript, Tailwind CSS, Pinia, and Lucide icons.
- **Development:** Docker Compose, isolated PostgreSQL tests, Laravel Pint, Prettier, and GitHub Actions.

Read the [frontend architecture guide](frontend/README.md) and [backend architecture guide](backend/README.md) for the application structure. See [CONTRIBUTING.md](CONTRIBUTING.md) for the development workflow.

## Quick start with Docker

Requirements: Docker Engine/Desktop with Docker Compose and Git.

```sh
docker compose up --build -d
```

Open [the workspace](http://localhost:3000). Laravel runs at `http://localhost:8000/api`. PostgreSQL runs in Docker and is also available locally at `127.0.0.1:5432`. Startup migrates the database and seeds demo data; named volumes persist the database, application storage, and Laravel application key. This Compose configuration is a development environment.

The login page includes prefilled demo credentials and shortcuts for Admin, Manager, and Staff accounts. Demo account definitions are available in the [authentication constants](frontend/constants/auth.ts) and [database seeder](backend/database/seeders/DatabaseSeeder.php).

```sh
docker compose ps
docker compose logs -f api frontend
docker compose down
```

`docker compose down` retains data. Adding `--volumes` removes it. Source files are mounted into development containers. If Windows file watching misses a frontend edit, use `docker compose restart frontend`. Rebuild after dependency changes with `docker compose up --build -d --renew-anon-volumes`; this renews anonymous frontend dependencies without deleting the named database volume.

## Run locally

Use PHP 8.4 with OpenSSL, mbstring, fileinfo, PDO PostgreSQL, SQLite (for unit tests), DOM, and XML support; Composer 2; and Node 22.13 or newer. Install dependencies from the committed lockfiles.

```sh
cd backend
composer install
cp .env.example .env
php artisan key:generate
docker compose -f ../compose.yaml up -d postgres
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

The backend example environment connects to local PostgreSQL using the `pms` database and schema. Docker initializes the schema on a fresh database volume. Set `POSTGRES_LOCAL_PORT` when port 5432 is occupied, and match `DB_PORT` when running Laravel outside Docker. On PowerShell, replace `cp` with `Copy-Item`. PHP must load a valid configuration; inspect it with `php --ini`.

In another terminal:

```sh
cd frontend
npm ci
cp .env.example .env
npm run dev
```

Nuxt proxies `/api` to Laravel. `API_PROXY_TARGET` defaults to `http://127.0.0.1:8000/api`; Compose supplies `http://api:8000/api`. To preview the built frontend, run `npm run build` and `node .output/server/index.mjs`. Set `API_PROXY_TARGET` in the process environment when running that server against a different API. A production secure session cookie requires HTTPS.

## Workspace pages

| Page | URL |
| --- | --- |
| Dashboard | `/dashboard` |
| Projects | `/projects` |
| Tasks and Kanban | `/tasks` |
| Clients | `/clients` |
| Reports | `/reports` |
| Team | `/team` |

Each section owns its page, components, state, service, and page composable. `/` redirects to the dashboard; `/login` preserves the requested destination. Record links such as `/projects?record=12` open details, and `/projects?create=1` opens a creation form.

## Features and permissions

- Login/logout with 12-hour Sanctum tokens, login rate limiting, and hashed passwords.
- Client search, pagination, contacts, and associated projects.
- Project clients, status, priority, dates, members, and progress calculated from completed tasks.
- Task filtering, assignment, status, priority, due dates, Kanban drag and drop, status selection, comments, and activity.
- Dashboard and reports with active/completed projects, overdue tasks, workload, status charts, and recent activity.
- Responsive pages, validation messages, empty/loading states, and delete confirmation.

| Action | Admin | Manager | Staff |
| --- | --- | --- | --- |
| Read clients/projects/tasks | All | All | Member projects and their clients/tasks |
| Create/edit/delete clients/projects/tasks | Yes | Yes | No |
| Assign project members and task owners | Yes | Yes | No |
| Update task status | Yes | Yes | Own tasks in member projects |
| Comment on tasks | Yes | Yes | Within member projects |
| Read team directory | Yes | Yes | Yes |
| Manage user accounts | Yes | No | No |
| Dashboard/activity | All work | All work | Member project work |

Admins create accounts in Team; there is no public registration. Self-deletion and demoting/deleting the last admin are blocked. Assignees must be project members; reassign tasks before removing their owner from a project. Clients with projects cannot be deleted. Deleting a project removes its tasks, comments, and project activity.

## API

The backend includes an [OpenAPI specification](backend/docs/openapi.json), [Postman collection](backend/docs/postman/ProjectManagement.postman_collection.json), and [local Postman environment](backend/docs/postman/Local.postman_environment.json). See the [API guide](backend/docs/README.md) for usage.

Send `Accept: application/json`. Login returns `{ user, token }`; authenticated requests require `Authorization: Bearer <token>`. Logout revokes the current token.

| Method | Endpoint | Purpose |
| --- | --- | --- |
| POST | `/api/login` | Login |
| GET | `/api/me` | Current user |
| POST | `/api/logout` | Revoke token |
| GET / POST | `/api/clients`, `/api/projects`, `/api/tasks`, `/api/users` | List/create |
| GET / PATCH / PUT / DELETE | `/api/{resource}/{id}` | Detail/update/delete |
| POST | `/api/tasks/{id}/comments` | Add comment using `body` |
| GET | `/api/activity` | Activity, optionally filtered by `task_id` |
| GET | `/api/dashboard` | Scoped reports |

Resource lists accept `search`, `page`, and `per_page` (1-100). Projects also accept `status`, `priority`, and `client_id`; tasks also accept `status`, `priority`, `project_id`, and `assignee_id`. Activity accepts `task_id`, `page`, and `per_page`. Lists return `data`, `links`, and `meta`; resource detail/create/update returns `data`; dashboard is unwrapped. Creation returns 201 and deletion returns 204. Validation returns 422 with `message` and field `errors`; authorization returns 401/403; inaccessible or missing detail records return 404.

Project statuses: `planning`, `active`, `on_hold`, `completed`. Task statuses: `todo`, `in_progress`, `review`, `done`. Priorities: `low`, `medium`, `high`, `urgent`. Project payloads accept `member_ids`; client contacts contain name, email, and optional phone.

## Checks

From the repository root using Docker:

```sh
docker compose config --quiet
docker compose exec -e DB_CONNECTION=sqlite -e DB_DATABASE=:memory: -e DB_URL= api php artisan test
docker compose exec api php vendor/bin/pint --test app routes tests
docker compose exec api composer validate --strict
docker compose exec frontend npm run format:check
docker compose exec frontend npm test
docker compose exec frontend npm run typecheck
docker compose exec frontend npm run build
```

Run `docker compose -f compose.test.yaml up --build --abort-on-container-exit --exit-code-from tests` to check against disposable PostgreSQL, then `docker compose -f compose.test.yaml down`. The SQLite suite remains available as a fast additional check. Both are isolated from development data. Run Nuxt type checking and build sequentially because both generate `.nuxt` files.

GitHub Actions runs the application checks on pushes and pull requests.

## Deployment

For Docker on a server with Supabase Postgres and automatic HTTPS, follow the [deployment guide](docs/DEPLOYMENT.md). Production images, Compose configuration, and environment examples are included.

## Development environment

Docker Compose and seeded accounts are intended for local development. Local environment files, dependencies, generated output, databases, and logs are excluded from Git.
