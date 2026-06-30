# Load .env so targets can use $(WP_URL) etc. Absent on fresh clone — guarded.
ifneq (,$(wildcard .env))
include .env
export
endif

WP = docker compose exec -T wpcli wp

.PHONY: up down install debug-on logs clean

up:
	docker compose up -d

down:
	docker compose down

install:
	$(WP) core install \
	  --url="$(WP_URL)" \
	  --title="$(WP_TITLE)" \
	  --admin_user="$(WP_ADMIN_USER)" \
	  --admin_password="$(WP_ADMIN_PASSWORD)" \
	  --admin_email="$(WP_ADMIN_EMAIL)" \
	  --skip-email
	$(WP) plugin install query-monitor --activate
	$(WP) theme activate starter
	$(WP) rewrite structure '/%postname%/' --hard
	@id=$$($(WP) post create --post_type=page --post_title="Dashboard" --post_name=dashboard --post_status=publish --porcelain); \
	$(WP) post meta update $$id _wp_page_template template-dashboard.php; \
	$(WP) menu create "Primary" 2>/dev/null; true; \
	$(WP) menu location assign Primary primary 2>/dev/null; true; \
	$(WP) menu item add-custom Primary "Home" / 2>/dev/null; true; \
	$(WP) menu item add-post Primary $$id 2>/dev/null; true

debug-on:
	$(WP) config set WP_DEBUG true --raw --type=constant
	$(WP) config set WP_DEBUG_LOG true --raw --type=constant
	$(WP) config set WP_DEBUG_DISPLAY true --raw --type=constant
	$(WP) config set SCRIPT_DEBUG true --raw --type=constant

logs:
	docker compose logs -f

clean:
	docker compose down -v
