# Frontend structure

The [production deployment](../docs/DEPLOYMENT.md) runs the built Nuxt server and proxies API requests to Laravel. Demo login defaults are disabled in production; `NUXT_PUBLIC_DEMO_LOGIN` controls their visibility.

Each section owns a Nuxt page, Pinia store, API service, page composable, and component folder.

| URL          | Page                        | Store / service | Components / composables |
| ------------ | --------------------------- | --------------- | ------------------------ |
| `/dashboard` | `pages/dashboard/index.vue` | `dashboard.ts`  | `dashboard/`             |
| `/projects`  | `pages/projects/index.vue`  | `projects.ts`   | `projects/`              |
| `/tasks`     | `pages/tasks/index.vue`     | `tasks.ts`      | `tasks/`                 |
| `/clients`   | `pages/clients/index.vue`   | `clients.ts`    | `clients/`               |
| `/reports`   | `pages/reports/index.vue`   | `reports.ts`    | `reports/`               |
| `/team`      | `pages/team/index.vue`      | `team.ts`       | `team/`                  |

`app.vue` renders `NuxtLayout` and `NuxtPage`. `layouts/workspace.vue` owns navigation and the header. `/` redirects to `/dashboard`; `/login` has its own layout. `middleware/auth.global.ts` restores authentication and preserves the requested URL through login.

## Responsibilities

- `pages/`: routes, titles, and layout selection.
- `components/<section>/`: page views, filters, tables, forms, and specialized interactions.
- `stores/<section>.ts`: independent filters, pagination, records, lookups, loading, and errors. Filters survive section navigation; state resets when the authenticated user changes.
- `services/<section>.ts`: typed API requests. Team uses the backend `/users` endpoint.
- `composables/<section>/use*Page.ts`: coordinates its store, service, forms, and actions. Nested composables are explicitly imported.
- `components/shared/`: resource dialogs, common form fields, headings, and reporting widgets.
- `components/ui/`: reusable UI primitives. `components/layout/`: sidebar and header.
- `composables/shared/`: reusable pagination, search debounce, session reset, resource actions, and record-link handling. Each feature creates its own instance.
- `services/shared/resource.ts`: reusable CRUD and paginated lookups bound to a fixed endpoint.
- `types/`: domain records, API contracts, and form/UI interfaces.
- `constants/`: navigation metadata, statuses, roles, colors, and defaults.
- `utils/`: pure formatting, payloads, errors, permissions, and route helpers.
- `composables/useApi.ts` and `server/routes/api/`: authenticated transport and Laravel proxy.

Services have no UI state. Forms edit independent drafts. Laravel enforces authorization; frontend permissions control available UI actions. Sequence checks prevent stale responses from replacing newer results, and search timers are disposed on navigation.

Navigation uses real links with active-route styling and browser history. `/projects?record=12` opens a project's details after loading. `/projects?create=1` opens the creation form. Related records navigate to their owning section.

To add a feature, create its route, store, service, page composable, and component folder, then add navigation metadata and its path. Put behavior in shared folders when multiple features need it.

## Shared constants

Import shared values from their dedicated module:

| Module                    | Shared values                                                                         |
| ------------------------- | ------------------------------------------------------------------------------------- |
| `constants/routes.ts`     | Workspace paths, login path, default landing page                                     |
| `constants/api.ts`        | API endpoint names and HTTP status codes                                              |
| `constants/domain.ts`     | Roles, project/task statuses, priorities, task views, Kanban columns and chart colors |
| `constants/pagination.ts` | List, board, lookup and recent-project limits; search debounce; empty pagination      |
| `constants/forms.ts`      | Allowed payload fields and input length limits                                        |
| `constants/reporting.ts`  | Empty dashboard values                                                                |
| `constants/app.ts`        | Display locale and time zone                                                          |
| `constants/auth.ts`       | Session cookie settings and demo login values                                         |
| `constants/workspace.ts`  | Navigation labels/icons, headings, descriptions and resource labels                   |

For example, use `USER_ROLE.ADMIN`, `TASK_STATUS.DONE`, `WORKSPACE_PATHS.projects`, and `API_ENDPOINTS.USERS` instead of repeating their string values. Domain types derive from the constant maps. Keep runtime API configuration in `nuxt.config.ts`; shared constants do not replace environment settings. Pure helpers import dependency-free constants and remain usable in Node tests.

## Page API requests

| Page      | Requests when visited                                         |
| --------- | ------------------------------------------------------------- |
| Clients   | `GET /clients`                                                |
| Team      | `GET /users`                                                  |
| Projects  | `GET /projects`                                               |
| Tasks     | `GET /tasks`, paginated project lookups for the filter        |
| Dashboard | `GET /dashboard`, `GET /activity`, `GET /projects?per_page=5` |
| Reports   | `GET /dashboard`, `GET /activity`                             |

Client and user lookups on Projects load only when opening a create/edit form. Task project lookups are needed by the visible project filter and task form. Lookup lists fetch additional pages only when necessary. Details and task activity load when opening a record; mutations refresh the affected page.

The layout makes no API requests. `stores/navigation.ts` reuses the total from unfiltered project lists or dashboard recent-project responses for the sidebar badge. The badge stays hidden until a total is known and resets on session changes. Session restoration may call `GET /me` on a full page reload; it does not run on every navigation.

## Validation

Run from this directory with Node 22.13 or newer:

```sh
npm run format       # Format source files
npm run format:check # Check formatting without changes
npm test
npm run typecheck
npm run build
```

Tests use Node's built-in runner and TypeScript stripping. In Docker, prefix commands with `docker compose exec frontend` from the repository root.
