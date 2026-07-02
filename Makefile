.PHONY: up down install logs clean

up:
	docker compose up -d --wait

down:
	docker compose down --remove-orphans

install:
	docker compose run --rm -T --entrypoint sh wpcli -ec 'if ! wp core is-installed; then \
			wp core install \
				--url="$$WP_URL" \
				--title="$$WP_TITLE" \
				--admin_user="$$WP_ADMIN_USER" \
				--admin_password="$$WP_ADMIN_PASSWORD" \
				--admin_email="$$WP_ADMIN_EMAIL" \
				--skip-email; \
		fi; \
		wp option update home "$$WP_URL"; \
		wp option update siteurl "$$WP_URL"; \
		wp theme activate starter; \
		wp rewrite structure "/%postname%/" --hard'

logs:
	docker compose logs -f

clean:
	@test "$(CONFIRM)" = "1" || { echo "Refusing volume deletion. Re-run with CONFIRM=1."; exit 1; }
	docker compose down -v --remove-orphans
