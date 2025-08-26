# Car Management API - Make Commands

.PHONY: help build up down restart logs clean test migrate seed

# Default target
help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-20s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

# Development commands
build: ## Build all Docker containers
	docker-compose build

up: ## Start all services in development mode
	docker-compose up -d
	@echo "Services started. API available at http://localhost:8080"
	@echo "Adminer available at http://localhost:8081"

down: ## Stop all services
	docker-compose down

restart: down up ## Restart all services

logs: ## Show logs from all services
	docker-compose logs -f

logs-app: ## Show logs from app service only
	docker-compose logs -f app

logs-nginx: ## Show logs from nginx service only
	docker-compose logs -f nginx

# Production commands
prod-build: ## Build for production
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml build

prod-up: ## Start services in production mode
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
	@echo "Production services started"

prod-down: ## Stop production services
	docker-compose -f docker-compose.yml -f docker-compose.prod.yml down

# Database commands
migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

migrate-fresh: ## Fresh migration with seeding
	docker-compose exec app php artisan migrate:fresh --seed

seed: ## Run database seeders
	docker-compose exec app php artisan db:seed

rollback: ## Rollback last migration
	docker-compose exec app php artisan migrate:rollback

# Development helpers
shell: ## Access app container shell
	docker-compose exec app /bin/sh

composer-install: ## Install composer dependencies
	docker-compose exec app composer install

composer-update: ## Update composer dependencies
	docker-compose exec app composer update

key-generate: ## Generate application key
	docker-compose exec app php artisan key:generate

db_seed: ## seed data
	docker-compose exec app php artisan db:seed

user: ## Create test user and show access token
	@echo "Creating test user..."
	@docker-compose exec -T app php artisan tinker --execute="\
	\$$user = \App\Models\User::firstOrCreate([\
		'email' => 'test@example.com'\
	], [\
		'name' => 'Test User',\
		'password' => \Hash::make('password123')\
	]);\
	\$$token = \$$user->createToken('API Token')->plainTextToken;\
	echo 'User created successfully!' . PHP_EOL;\
	echo 'Email: test@example.com' . PHP_EOL;\
	echo 'Password: password123' . PHP_EOL;\
	echo 'Access Token: ' . \$$token . PHP_EOL;"

# Cache commands
cache-clear: ## Clear all caches
	docker-compose exec app php artisan optimize:clear

cache-optimize: ## Optimize for production
	docker-compose exec app php artisan optimize

# Queue commands
queue-work: ## Start queue worker (for debugging)
	docker-compose exec app php artisan queue:work --verbose

queue-restart: ## Restart all queue workers
	docker-compose exec app php artisan queue:restart

queue-failed: ## Show failed queue jobs
	docker-compose exec app php artisan queue:failed

queue-retry: ## Retry all failed jobs
	docker-compose exec app php artisan queue:retry all

# Testing commands
test: ## Run all tests
	docker-compose exec app php artisan test

test-coverage: ## Run tests with coverage
	docker-compose exec app php artisan test --coverage

test-feature: ## Run only feature tests
	docker-compose exec app php artisan test tests/Feature

test-unit: ## Run only unit tests
	docker-compose exec app php artisan test tests/Unit

# Maintenance commands
clean: ## Clean up Docker resources
	docker-compose down -v
	docker system prune -f
	docker volume prune -f

status: ## Show service status
	docker-compose ps

stats: ## Show container resource usage
	docker stats

# Setup commands
setup: build up key-generate migrate seed ## Complete setup for new installation
	@echo "Setup complete! API is ready at http://localhost:8080"

setup-prod: prod-build prod-up migrate ## Setup for production
	@echo "Production setup complete!"

# Swagger commands
swagger-generate: ## Generate Swagger documentation
	docker-compose exec app php artisan l5-swagger:generate

swagger-publish: ## Publish Swagger assets
	docker-compose exec app php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"

# Backup commands
backup-db: ## Backup database
	docker-compose exec database mysqldump -u car_user -pcar_password car_management > backup_$(shell date +%Y%m%d_%H%M%S).sql

restore-db: ## Restore database (requires BACKUP_FILE variable)
	@if [ -z "$(BACKUP_FILE)" ]; then echo "Please specify BACKUP_FILE=filename.sql"; exit 1; fi
	docker-compose exec -T database mysql -u car_user -pcar_password car_management < $(BACKUP_FILE)

# Monitoring commands
monitor: ## Monitor logs and stats
	@echo "Starting monitoring... Press Ctrl+C to stop"
	@trap 'echo "Stopping monitoring..."' INT; \
	docker-compose logs -f &
	docker stats
