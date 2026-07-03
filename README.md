# WordPress Docker Starter Kit

Local WordPress development stack built from official Docker images:

- WordPress 7 on PHP 8.3/Apache: http://localhost
- phpMyAdmin 5.2: http://localhost:8080
- MySQL 8.4, private to the Compose network
- Disposable WP-CLI container for maintenance and automation
- Narrow-mounted `starter` theme for project code

> **Local development only.** This stack has no HTTPS, production secrets,
> backups, deployment workflow, or production hardening. Do not expose it to a
> public network or deploy it as-is.

## Requirements

- Docker with Compose v2 (`docker compose`)
- GNU Make
- Free local ports 80 and 8080, unless overridden

## Setup

```bash
cp .env.example .env
# Edit .env and replace the local credentials.
make up
make install
```

Open WordPress at http://localhost and its admin at
http://localhost/wp-admin. `WP_ADMIN_USER`, `WP_ADMIN_PASSWORD`, and
`WP_ADMIN_EMAIL` are used only during the initial installation. Editing them in
`.env` later does not change an existing WordPress user.

To change an existing user's password without putting it in shell history:

```bash
docker compose run --rm wpcli user update <username> --prompt=user_pass
```

Alternatively, reset the whole local site with `make clean CONFIRM=1`, then run
`make up` and `make install` with the new initial credentials.

Open phpMyAdmin at http://localhost:8080. Log in with `DB_USER` and
`DB_PASSWORD`; use `db` as the server/host if prompted.

`make install` is repeatable. It installs WordPress only when core is not yet
installed, then always aligns `home` and `siteurl` with `WP_URL`, activates the
`starter` theme, and sets the permalink structure.

## Commands

| Command | Purpose |
|---|---|
| `make up` | Start services and wait for health checks |
| `make down` | Stop services while keeping data |
| `make install` | Install or reconcile the local WordPress site |
| `make logs` | Follow service logs |
| `make clean CONFIRM=1` | Stop services and permanently delete named volumes |

Run WP-CLI in a disposable container:

```bash
docker compose run --rm wpcli plugin list
docker compose run --rm wpcli core version
```

The image entrypoint is already `wp`; do not add another `wp` argument.

The WP-CLI image uses MariaDB client utilities, which reject MySQL's generated
self-signed certificate. For local `wp db` commands that invoke those
utilities, disable certificate verification explicitly:

```bash
docker compose run --rm wpcli db check --ssl-verify-server-cert=0
```

This override is only for the private local Compose network. Production
database clients should verify a trusted CA.

## Data and custom code

Database files persist in `db_data`. WordPress core, plugins, and uploads
persist in `wp_data`. `make down` keeps both volumes. To reset the site
completely, run:

```bash
make clean CONFIRM=1
make up
make install
```

Only `wp-content/themes/starter` is bind-mounted from this repository, read
only. Core, uploads, installed plugins, and other themes live in named volumes
and are not project source. For future custom themes, plugins, or mu-plugins,
add narrow bind mounts for their individual directories under `volumes:` on
both the `wordpress` and `wpcli` services.

## Ports and URLs

Override `WP_PORT` or `PMA_PORT` in `.env` when ports 80 or 8080 are occupied.
When changing `WP_PORT`, keep `WP_URL` aligned, including the port:

```dotenv
WP_PORT=8081
WP_URL=http://localhost:8081
PMA_PORT=8082
```

Then run `make up` and `make install` so WordPress stores the updated URL.

## Image updates

Pull official service images, rebuild the small WP-CLI derivative, then
recreate services:

```bash
docker compose --profile tools pull
docker compose build --pull wpcli
make up
```

Image tags can bring upstream changes. Read WordPress, MySQL, phpMyAdmin, and
WP-CLI release notes before updating or publishing a release of this kit, and
test with disposable data first.
