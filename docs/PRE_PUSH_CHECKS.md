# Pre-push verification

Application checks were refreshed October 5, 2026, including the production Docker setup and PostgreSQL compatibility.

## Completed checks

| Check | Result |
| --- | --- |
| Backend tests | 21 passed, 141 assertions on SQLite and isolated PostgreSQL |
| Backend formatting | Laravel Pint passed for app, routes, and tests |
| Composer manifest | Strict validation passed |
| API reference | OpenAPI and Postman formats validated; all 29 route operations covered |
| Frontend tests | 7 passed |
| Frontend formatting | Prettier check passed |
| Frontend type checking | Passed |
| Frontend production build | Passed on Windows and fresh Linux container |
| Docker Compose configuration | Valid |
| Production Docker | API and frontend images built; proxy CRUD, comments, reports, logout, and admin creation passed |
| Supabase preparation | Migrations verified in a private `pms` schema using local Postgres; live connection requires deployment credentials |
| Docker images | Backend and frontend built successfully from lockfiles |
| Fresh-container checks | Backend and frontend CI-equivalent commands passed |
| CI configuration | YAML parsed; push/PR triggers, read-only permissions, timeouts, and concurrency configured |
| Repository hygiene | Local environments, dependencies, generated output, databases, logs, private keys, and scratch tools excluded |
| Credential pattern scan | No matches in the candidate source/documentation files |
| Staged-file review | No excluded artifacts, broken local documentation links, or whitespace errors |

Browser verification covered project/task creation and deletion, Kanban dragging, comments, team listing/search/details, authenticated reload, logout, staff controls, page-specific API requests, related-record links, mobile navigation, and client modal dividers. These checks used the running development application; browser harnesses and screenshots remain local in ignored scratch tooling.

The credential scan checks common private-key, GitHub-token, cloud-key, and application-key patterns. Demo credentials in Compose and the documentation are intentional development fixtures.

## GitHub handoff

The repository uses `main`. Documentation includes setup, feature structure, API contracts, permission rules, testing commands, OpenAPI and Postman files, and contribution guidance.

GitHub repository: [bryanlollers/project-management-system](https://github.com/bryanlollers/project-management-system). Local application checks passed before the initial commit. Hosted workflow results are available in the repository's Actions tab after pushing.
