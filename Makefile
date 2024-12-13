DOCKER = docker
DOCKER_COMPOSE = docker-compose
EXEC = $(DOCKER) exec -w /app php
PHP = $(EXEC) php
COMPOSER = $(EXEC) composer
SYMFONY_CONSOLE = $(PHP) bin/console

## —— 🔥 App ——
init: ## Инициализация проекта
	$(MAKE) start
	$(MAKE) composer-install
	@/bin/echo "Приложение доступно по адресу: http://127.0.0.1:7777/."

cache-clear: ## Очистка кэша
	$(SYMFONY_CONSOLE) cache:clear

## —— ✅ Test ——
.PHONY: tests
tests: ## Запуск тестов
	$(MAKE) database-init-test
	$(PHP) bin/phpunit

database-init-test: ## Инициализация базы данных для теста
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists --env=test
	$(SYMFONY_CONSOLE) d:d:c --env=test
	$(SYMFONY_CONSOLE) d:m:m --no-interaction --env=test
	$(SYMFONY_CONSOLE) d:f:l --no-interaction --env=test

## —— 🐳 Docker ——
start: ## Запуск приложения
	$(MAKE) docker-start
docker-start:
	$(DOCKER_COMPOSE) up -d

stop: ## Остановка приложения
	$(MAKE) docker-stop
docker-stop:
	$(DOCKER_COMPOSE) stop
	@/bin/echo "Контейнеры остановлены."

## —— 🎻 Composer ——
composer-install: ## Установка зависимостей
	$(COMPOSER) install

composer-update: ## Обновление зависимостей
	$(COMPOSER) update

## —— 📊 Database ——
database-init: ## Инициализация базы данных
	$(MAKE) database-drop
	$(MAKE) database-create
	$(MAKE) database-migrate
	$(MAKE) database-fixtures-load

database-create: ## Создание базы данных
	$(SYMFONY_CONSOLE) d:d:c --if-not-exists

database-drop: ## Удаление базы данных
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists

database-migration: ## Создание миграции
	$(SYMFONY_CONSOLE) make:migration

migration: ## Псевдоним: database-migration
	$(MAKE) database-migration

database-migrate: ## Применение миграций
	$(SYMFONY_CONSOLE) d:m:m --no-interaction

migrate: ## Псевдоним: database-migrate
	$(MAKE) database-migrate

database-fixtures-load: ## Загрузка фикстур
	$(SYMFONY_CONSOLE) d:f:l --no-interaction

fixtures: ## Псевдоним: database-fixtures-load
	$(MAKE) database-fixtures-load

## —— 🛠️  Others ——
help: ## Список команд
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
