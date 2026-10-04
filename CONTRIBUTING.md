# Contributing

Start with the [root README](README.md), then read the [frontend](frontend/README.md) or [backend](backend/README.md) architecture guide for the feature you are changing.

## Development workflow

1. Create a branch for one feature or fix.
2. Keep feature code in its own folders.
3. Update the OpenAPI specification, Postman collection, and frontend contracts when changing API endpoints or payloads.
4. Add regression tests for business rules and observable behavior. Avoid tests that only repeat implementation details.
5. Run formatting, tests, type checking, and the production build before opening a pull request.

## Local checks

In `backend/`:

```sh
composer validate --strict
composer format:check
composer test
```

In `frontend/`:

```sh
npm ci
npm run format:check
npm test
npm run typecheck
npm run build
```

Use `composer format` or `npm run format` to apply formatting. Type checking and build must run sequentially. Docker equivalents are in the root README. Backend tests use isolated SQLite, not the demo database.

For changes to navigation, forms, dialogs, authentication, or Kanban, also verify the affected browser flows and a narrow/mobile viewport. Check network requests when changing page loaders.

## Pull requests

Describe the concrete problem, resulting behavior, and verification. Include screenshots when the visible UI changes. Keep credentials, local environments, generated output, dependency folders, databases, and logs out of commits. Commit lockfiles when dependencies change.

## Initial GitHub push

The local repository uses `main`. Review the files and the [pre-push report](docs/PRE_PUSH_CHECKS.md), create an empty GitHub repository, then configure its URL:

```sh
git status
git add .
git diff --cached --check
git diff --cached --stat
git commit -m "Initial project management system"
git remote add origin <repository-url>
git push -u origin main
```

Configure your Git author name/email before committing if they are not already set. Avoid creating a separate README in the GitHub repository so the first push starts from the same history. No remote or push is performed by the local verification workflow.
