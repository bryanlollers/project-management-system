# Backend structure

For production Docker hosting with Supabase Postgres, see the [deployment guide](../docs/DEPLOYMENT.md). Database schema and SSL settings are configurable through `DB_SCHEMA`, `DB_SSLMODE`, and `DB_SSLROOTCERT`. Create the first administrator with `php artisan app:create-admin` after migrations.

Local development also uses PostgreSQL 17 with a `pms` database and schema. Compose creates the schema on the first database startup. The example environment connects to the Docker database from a locally running Laravel process; the API container uses the `postgres` service host. Run the isolated PostgreSQL suite with `docker compose -f compose.test.yaml up --build --abort-on-container-exit --exit-code-from tests` from the repository root.

Each API feature owns its controller, service, request validation, and response resources. `routes/api.php` registers explicit controllers; there is no resource-kind dispatcher.

| Feature | Controllers | Services | Requests | Resources |
| --- | --- | --- | --- | --- |
| Authentication | `Auth/AuthController.php` | `Auth/AuthService.php` | `Auth/LoginRequest.php` | `Auth/SessionResource.php` and `Users/UserResource.php` |
| Clients | `Clients/ClientController.php` | `Clients/ClientService.php` | `Clients/IndexClientRequest.php`, `SaveClientRequest.php` | `Clients/ClientResource.php` |
| Projects | `Projects/ProjectController.php` | `Projects/ProjectService.php` | `Projects/IndexProjectRequest.php`, `SaveProjectRequest.php` | `Projects/ProjectResource.php` |
| Tasks | `Tasks/TaskController.php` | `Tasks/TaskService.php` | `Tasks/IndexTaskRequest.php`, `SaveTaskRequest.php` | `Tasks/TaskResource.php` |
| Team accounts | `Users/UserController.php` | `Users/UserService.php` | `Users/IndexUserRequest.php`, `SaveUserRequest.php` | `Users/UserResource.php` |
| Comments | `Comments/CommentController.php` | `Comments/CommentService.php` | `Comments/StoreCommentRequest.php` | `Comments/CommentResource.php` |
| Activity | `Activity/ActivityController.php` | `Activity/ActivityService.php` | `Activity/IndexActivityRequest.php` | `Activity/ActivityResource.php` |
| Dashboard/reporting | `Dashboard/DashboardController.php` | `Dashboard/DashboardService.php` | No input payload | `Dashboard/DashboardResource.php` |

Controller and request/resource paths are relative to `app/Http/`; service paths are relative to `app/Services/`.

## Responsibilities

- Controllers accept validated requests, delegate to their feature service, and return resources with the appropriate HTTP status.
- Services own transactions and business rules: task assignee membership, project team changes, account deletion/demotion guards, and activity recording.
- `app/Queries/{Clients,Projects,Tasks,Users}/` owns feature-specific visibility, search, filters, eager loading, pagination, and detail queries. Dashboard reuses project/task visibility queries for its aggregates.
- `app/Http/Requests/<Feature>/Save*Request.php` validates both creation and partial updates and authorizes them through the matching policy. Project date validation checks the merged stored/submitted dates. Staff task updates permit only the status field.
- `app/Http/Requests/Shared/PaginatedRequest.php` provides common list input validation. Each feature adds only its supported filters.
- `app/Http/Resources/<Feature>/` explicitly selects public fields and serializes loaded relationships through their own resources. Resources do not run queries. Project progress uses loaded counts or tasks.
- `app/Policies/` controls resource permissions. Admins and managers manage clients/projects/tasks; only admins manage accounts. Staff visibility is limited to their project memberships, and staff may update only assigned task statuses. Authenticated users can read team accounts.
- `app/Services/Activity/ActivityService.php` records activity for clients, projects, tasks, and comments. It also owns the scoped activity feed.
- `app/Models/` owns Eloquent relationships and casts.
- `tests/Feature/<Feature>/` contains endpoint regression tests. Shared fixtures live in `tests/Concerns/CreatesWorkspaceFixtures.php`.

Laravel resolves services and queries through constructor injection. Policy discovery follows the model/policy naming convention.

## API reference

Import the [OpenAPI specification](docs/openapi.json) into an API documentation viewer, or use the [Postman collection](docs/postman/ProjectManagement.postman_collection.json) with its [local environment](docs/postman/Local.postman_environment.json). The [API guide](docs/README.md) explains authentication and request ordering.

## API contracts

Existing paths remain `/api/clients`, `/api/projects`, `/api/tasks`, and `/api/users`, with numeric route model binding. Lists accept search and pagination; projects add status/priority/client filters, and tasks add status/priority/project/assignee filters. CRUD resources return `{ data: ... }`; paginated lists also include `links` and `meta`. Creation returns 201 and deletion returns 204. Updates accept PATCH and PUT.

`POST /api/tasks/{task}/comments` belongs to the comments feature. `/api/activity` supports task filtering and pagination. `/api/dashboard` remains an unwrapped summary object; the frontend Reports page uses this endpoint too. Login and session restoration retain `{ user, token }` and `{ user }` responses. Laravel Sanctum protects all routes except login.

To add a feature, create its controller, service, requests, resources, query (when needed), policy, and tests, then register its explicit route. Shared code should represent reusable mechanics rather than dispatching on resource type.

## Validation and formatting

From the repository root, run backend tests against in-memory SQLite:

```sh
docker compose exec -e DB_CONNECTION=sqlite -e DB_DATABASE=:memory: -e DB_URL= api php artisan test
docker compose exec api php vendor/bin/pint --test app routes tests
```

From `backend/` with the required PHP extensions:

```sh
composer test
composer format
composer format:check
```

The test suite covers authentication, resource payloads, CRUD, pagination, permissions, staff visibility, task/project membership rules, project date updates, protected accounts, activity, and reports.
