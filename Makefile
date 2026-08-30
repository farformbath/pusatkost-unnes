.PHONY: setup up down restart logs shell test lint analyse fresh types

setup:
	cp -n .env.example .env || true
	docker compose up -d --build
	docker compose exec app composer install
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate:fresh --seed
	npm install
	npm run build

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart

logs:
	docker compose logs -f

shell:
	docker compose exec app sh

test:
	php artisan test
	npm run type-check

lint:
	./vendor/bin/pint --test

analyse:
	./vendor/bin/phpstan analyse

fresh:
	php artisan migrate:fresh --seed

types:
	php artisan typescript:transform
