# WordPress + Docker Starter Kit

Lean, reusable local-dev environment for WordPress — built to feel familiar if
you come from Laravel. Provisions **infrastructure + dev tooling only**.
Project-specific plugins (ACF, etc.) and real themes are added per-project.

- WordPress (PHP 8.2, Apache) on http://localhost:8080
- MySQL 8.0 with healthcheck
- Long-running WP-CLI service
- Query Monitor as the only pre-installed plugin
- A minimal, design-agnostic `starter` theme to build on or replace

## Setup (clone → running site)

```bash
git clone <your-repo-url> wordpress-docker-starter-kit && cd wordpress-docker-starter-kit
cp .env.example .env        # adjust credentials if you like
make up                     # start db + wordpress + wp-cli
make install                # wp core install + Query Monitor + activate starter theme
```

Open http://localhost:8080 (admin at `/wp-admin`, credentials from `.env`).

## Make targets

| Target | Does |
|---|---|
| `make up` | Start all services (detached) |
| `make down` | Stop services (keeps data) |
| `make install` | Non-interactive `wp core install`, install/activate Query Monitor, activate `starter` theme |
| `make debug-on` | Set `WP_DEBUG`, `WP_DEBUG_LOG`, `WP_DEBUG_DISPLAY`, `SCRIPT_DEBUG` |
| `make logs` | Tail logs |
| `make clean` | Stop **and delete volumes** (fresh start) |

Run any WP-CLI command directly:

```bash
docker compose exec wpcli wp plugin list
```

## What's committed vs. not

Core, uploads, cache, default themes/plugins, `.env`, and logs are gitignored.
You commit only **custom code** — the `starter` theme and any custom
plugins/mu-plugins you add under `wp-content/`.

## The `starter` theme

A near-empty, neutral base. `index.php` is WordPress's universal
template-hierarchy fallback, so this one theme already renders every route.
Add `single.php`, `page.php`, `archive.php`, etc. as your design grows — or
delete the theme and drop in your own. Nothing here is opinionated.

## WordPress request lifecycle, mapped to Laravel

Same request, different names. If you know the Laravel column, you know where
to hook in WordPress.

| Stage | Laravel | WordPress |
|---|---|---|
| Entry point | `public/index.php` | `index.php` → `wp-blog-header.php` |
| Bootstrap | `bootstrap/app.php`, kernel | `wp-load.php` → `wp-settings.php` |
| Providers / plugins | Service providers (`register`/`boot`) | Plugins + theme `functions.php` loaded; `plugins_loaded`, `init` |
| Routing / query | Router matches a route | `WP::main()` parses the URL → `WP_Query` (the "main query") |
| Middleware / hooks | Middleware pipeline | Actions & filters (`pre_get_posts`, `template_redirect`, …) |
| Controller / template | Controller method | Template-hierarchy file (`single.php`, `page.php`, … or `index.php`) |
| View / output | Blade view | Theme template + The Loop render HTML |

Mental model: WordPress has **no explicit routes or controllers** — the URL is
resolved into a database query (`WP_Query`), and the matching template file
plays the role of the controller+view. Hooks are your middleware: they let you
intervene at any stage without editing core.
