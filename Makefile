init: down build up

up:
	docker compose up -d

down:
	docker compose down --remove-orphans

build:
	docker compose build --pull


# Composer section
app:
	docker compose run --rm api-php-cli composer app

iam:
	docker compose run --rm api-php-cli composer iam
