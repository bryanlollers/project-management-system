# Docker deployment with Supabase

The production stack runs Laravel under Apache, the built Nuxt server, and Caddy for HTTPS. Supabase provides Postgres. Authentication and authorization continue to use Laravel Sanctum and policies; Supabase Auth and its browser SDK are not required.

Local development uses PostgreSQL 17 and the same `pms` schema. Development and deployment share Laravel migrations; deploying a new database does not require a MySQL-to-Postgres conversion. Existing MySQL development records are not imported automatically. For development against hosted Supabase, use a separate project and its session-pooler credentials with `DB_SSLMODE=require`; never run development seeders or tests against the production project.

## Supabase database

1. Create a Supabase project. In **Connect**, copy the **Session pooler** host, username, database, and password. Use port `5432`. The session pooler supports IPv4 and prepared statements; direct connections are also suitable when the server supports IPv6. See [Supabase connection guidance](https://supabase.com/docs/guides/database/connecting-to-postgres).
2. Run [supabase-schema.sql](../deploy/supabase-schema.sql) in the Supabase SQL Editor. This creates the `pms` schema for Laravel tables.
3. Keep `pms` out of the Data API's exposed schemas. The application accesses it only through Laravel's server-side database connection. Supabase's `auth` and `storage` schemas remain separate. See [private schemas](https://supabase.com/docs/guides/database/tables).

The production configuration requires encrypted database connections. For certificate verification, mount Supabase's root certificate into the API container and set `DB_SSLROOTCERT` to its container path and `DB_SSLMODE=verify-full` in a Compose override.

## Server setup

Use a Linux server with Docker and Compose. Point a domain's DNS records at the server and allow inbound TCP ports 80 and 443; UDP 443 enables HTTP/3. Only Caddy publishes ports. Laravel and Nuxt are reachable inside the Docker network.

```sh
git clone https://github.com/bryanlollers/project-management-system.git
cd project-management-system
cp deploy/.env.example deploy/.env
```

Edit `deploy/.env` with your domain, certificate contact email, and Supabase connection parameters. Generate the application key once:

```sh
docker run --rm php:8.4-cli php -r 'echo "base64:".base64_encode(random_bytes(32)).PHP_EOL;'
```

Save it as `APP_KEY` and preserve it across releases. Quote passwords containing `$`, `#`, or spaces with single quotes in the environment file. Keep this file on the server; it is excluded from Git. Restrict permissions with `chmod 600 deploy/.env`.

## First release

```sh
docker compose --env-file deploy/.env -f compose.production.yaml config --quiet
docker compose --env-file deploy/.env -f compose.production.yaml build
docker compose --env-file deploy/.env -f compose.production.yaml run --rm api php artisan migrate --force
docker compose --env-file deploy/.env -f compose.production.yaml run --rm api php artisan app:create-admin
docker compose --env-file deploy/.env -f compose.production.yaml up -d
docker compose --env-file deploy/.env -f compose.production.yaml ps
```

The admin command prompts for a name, email, and password. It creates an account with a hashed password and refuses duplicate emails. Create the rest of the team through the Team page. Production never runs the demo seeder and hides demo login hints.

Visit `https://your-domain` and sign in. Check project creation, tasks, comments, and logout. The frontend proxies `/api` to Laravel, so no separate public API domain or browser CORS configuration is needed. Caddy obtains and renews certificates automatically; its volumes preserve certificates across restarts.

## Updates and operations

Back up the Supabase database before schema changes. Fetch the desired commit, rebuild, migrate, and recreate containers:

```sh
git pull --ff-only
docker compose --env-file deploy/.env -f compose.production.yaml build
docker compose --env-file deploy/.env -f compose.production.yaml run --rm api php artisan migrate --force
docker compose --env-file deploy/.env -f compose.production.yaml up -d --remove-orphans
docker compose --env-file deploy/.env -f compose.production.yaml logs --tail=100 api frontend caddy
```

Environment changes require recreating containers. Application caches are generated at startup using deployment variables; migrations are explicit release commands. `/up` is the API process health endpoint, not a database readiness check. Check database readiness using `php artisan migrate:status` inside the API container.

`APP_DEBUG` stays off, logs go to container stderr, and application storage persists in a volume. Queue jobs run synchronously in this deployment. Keep the server updated and arrange database backups in Supabase. Do not delete production volumes during routine updates.

Trusted proxy headers are accepted because the API is private to the Compose network. Do not publish its port directly. HTTPS is required for the frontend's secure session cookie.

## Verification

Run the backend suite against a disposable Postgres instance before releasing:

```sh
docker compose -f compose.test.yaml up --build --abort-on-container-exit --exit-code-from tests
docker compose -f compose.test.yaml down
```

The test database is temporary and separate from Supabase. CI runs backend tests against SQLite and Postgres. Production images can be built without live credentials; running migrations and HTTPS requires your configured Supabase project and domain.
