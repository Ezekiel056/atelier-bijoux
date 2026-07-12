tailwind:
	docker compose exec php bash -c "php bin/console tailwind:build --watch"

migration:
	docker compose --env-file app/.env exec php bash -c "php bin/console make:migration"

migrate:
	docker compose --env-file app/.env exec php bash -c "php bin/console doctrine:migrations:migrate --no-interaction"