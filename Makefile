.PHONY: up down restart stop start exec logs ps build clean help tools-install phpstan cs-fix cs-check test deptrac

# Docker Compose command
DC = docker-compose

# Default target
.DEFAULT_GOAL := help

## help: Show help
help:
	@echo "Available commands:"
	@echo ""
	@echo "Docker commands:"
	@echo "  make up         - Start all containers"
	@echo "  make down       - Stop and remove containers"
	@echo "  make stop       - Stop containers (without removing)"
	@echo "  make start      - Start stopped containers"
	@echo "  make restart    - Restart containers"
	@echo "  make exec       - Enter application container (gym-app)"
	@echo "  make exec-db    - Enter database container"
	@echo "  make exec-redis - Enter Redis container"
	@echo "  make logs       - Show logs from all containers"
	@echo "  make logs-app   - Show application logs"
	@echo "  make ps         - Show container status"
	@echo "  make build      - Build containers from scratch"
	@echo "  make clean      - Remove containers, volumes and networks"
	@echo ""
	@echo "Development tools:"
	@echo "  make tools-install - Install development tools from .tools/"
	@echo "  make phpstan       - Run PHPStan static analysis"
	@echo "  make cs-fix        - Fix code style with PHP-CS-Fixer"
	@echo "  make cs-check      - Check code style without fixing"
	@echo "  make deptrac       - Run Deptrac architecture analysis"
	@echo "  make test          - Run tests"

## up: Start all containers
up:
	$(DC) up -d

## down: Stop and remove containers
down:
	$(DC) down

## stop: Stop containers
stop:
	$(DC) stop

## start: Start stopped containers
start:
	$(DC) start

## restart: Restart containers
restart:
	$(DC) restart

## exec: Enter application container
exec:
	$(DC) exec app bash

## exec-db: Enter database container
exec-db:
	$(DC) exec database psql -U gym_user -d gym

## exec-redis: Enter Redis container
exec-redis:
	$(DC) exec redis redis-cli

## logs: Show logs from all containers
logs:
	$(DC) logs -f

## logs-app: Show application logs
logs-app:
	$(DC) logs -f app

## ps: Show container status
ps:
	$(DC) ps

## build: Build containers from scratch
build:
	$(DC) build

## clean: Remove containers, volumes and networks
clean:
	$(DC) down -v --remove-orphans

## tools-install: Install development tools from .tools/
tools-install:
	$(DC) exec app composer install --working-dir=.tools

## phpstan: Run PHPStan static analysis
phpstan:
	$(DC) exec app .tools/vendor/bin/phpstan analyse -l 6 src tests

## cs-fix: Fix code style with PHP-CS-Fixer
cs-fix:
	$(DC) exec app .tools/vendor/bin/php-cs-fixer fix --allow-risky=yes

## deptrac: Run Deptrac architecture analysis
deptrac:
	$(DC) exec app .tools/vendor/bin/deptrac analyse

## test: Run tests
test:
	$(DC) exec app vendor/bin/phpunit
