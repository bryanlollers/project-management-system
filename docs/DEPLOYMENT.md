# Docker deployment with MySQL

The production stack runs Laravel under Apache, the built Nuxt server, and Caddy for HTTPS. Configure an existing MySQL server reachable from the API container; the production Compose file does not provision a database. Authentication and authorization use Laravel Sanctum and policies.

Local development uses MySQL 8.4 and an `orbit` database. Development and deployment share Laravel migrations. Existing databases are not imported automatically when switching database engines; retain a backup and migrate any records you need separately.

## MySQL database

1. Provision a MySQL 8.4 database named `pms` and a dedicated application login with privileges to create and manage its tables. Restrict the login to the application server's network.
2. Set the host, port (`3306`), database, username, and password in `deploy/.env`. Use a separate database for development and tests.
3. For remote connections, configure TLS on the database server, mount its CA certificate read-only into the API container using a Compose override, and set `MYSQL_ATTR_SSL_CA` to the certificate's path inside the container. Require encrypted connections on the database login or server.

Laravel migrations create the application tables; no separate schema initialization script is needed.

## Server setup

Use a Linux server with Docker and Compose. Point a domain's DNS records at the server and allow inbound TCP ports 80 and 443; UDP 443 enables HTTP/3. Only Caddy publishes ports. Laravel and Nuxt are reachable inside the Docker network.

```sh
git clone https://github.com/bryanlollers/project-management-system.git
cd project-management-system
cp deploy/.env.example deploy/.env
```

Edit `deploy/.env` with your domain, certificate contact email, and MySQL connection parameters. Generate the application key once:

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

Back up the MySQL database before schema changes. Fetch the desired commit, rebuild, migrate, and recreate containers:

```sh
git pull --ff-only
docker compose --env-file deploy/.env -f compose.production.yaml build
docker compose --env-file deploy/.env -f compose.production.yaml run --rm api php artisan migrate --force
docker compose --env-file deploy/.env -f compose.production.yaml up -d --remove-orphans
docker compose --env-file deploy/.env -f compose.production.yaml logs --tail=100 api frontend caddy
```

Environment changes require recreating containers. Application caches are generated at startup using deployment variables; migrations are explicit release commands. `/up` is the API process health endpoint, not a database readiness check. Check database readiness using `php artisan migrate:status` inside the API container.

`APP_DEBUG` stays off, logs go to container stderr, and application storage persists in a volume. Queue jobs run synchronously in this deployment. Keep the server updated and arrange regular MySQL database backups. Do not delete production volumes during routine updates.

Trusted proxy headers are accepted because the API is private to the Compose network. Do not publish its port directly. HTTPS is required for the frontend's secure session cookie.

## Verification

Run the backend suite against a disposable MySQL instance before releasing:

```sh
docker compose -f compose.test.yaml up --build --abort-on-container-exit --exit-code-from tests
docker compose -f compose.test.yaml down
```

The test database is temporary and separate from production. CI runs backend tests against SQLite and MySQL. Production images can be built without live credentials; running migrations and HTTPS requires your configured MySQL server and domain.
