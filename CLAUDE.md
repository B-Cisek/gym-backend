# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Gym Management SaaS - Multi-tenant system for managing gym chains and fitness facilities.

**Stack:** Symfony 8.0 + PHP 8.5 + PostgreSQL 18.1 + Redis 8.0

**Architecture:** Layered architecture with strict separation between Domain, Application, Infrastructure, and Presentation layers. See ARCHITECTURE.md for full details.

## Development Commands

### Docker Environment

Start/stop services:
```bash
make up          # Start all containers
make down        # Stop and remove containers
make restart     # Restart containers
```

Access containers:
```bash
make exec        # Enter app container (bash)
make exec-db     # Enter database container (psql)
make exec-redis  # Enter Redis CLI
```

View logs:
```bash
make logs        # All containers
make logs-app    # Application only
```

### Testing & Quality

```bash
make test        # Run PHPUnit tests
make phpstan     # Run PHPStan static analysis (level 6)
make cs-fix      # Fix code style with PHP-CS-Fixer
make cs-check    # Check code style without fixing
make deptrac     # Run Deptrac architecture layer analysis
```

Run tests inside container:
```bash
docker-compose exec app vendor/bin/phpunit
```

### Database

```bash
docker-compose exec app bin/console doctrine:migrations:migrate
docker-compose exec app bin/console doctrine:migrations:diff
docker-compose exec app bin/console doctrine:schema:update --dump-sql
```

### Development Tools

Install development tools (PHPStan, PHP-CS-Fixer, Deptrac):
```bash
make tools-install
```

Tools are installed in `.tools/vendor/` directory to keep them separate from application dependencies.

## Architecture Overview

The codebase follows a **strict layered architecture** organized by business modules:

```
src/
├── Auth/            # Authentication & User management module
├── Shared/          # Cross-cutting concerns
│   ├── Domain/          # Value objects (Id, Email), interfaces
│   ├── Application/     # Shared services
│   ├── Infrastructure/  # EventDispatcher, ExceptionListener, utilities
│   └── Presentation/    # Shared HTTP concerns
```

### Layer Responsibilities

**Domain** (`*/Domain/`)
- Pure PHP business logic (no framework dependencies)
- Domain entities (immutable, readonly classes)
- Repository interfaces (no implementations)
- Value objects (Email, Id, UserRole)

**Application** (`*/Application/`)
- Use cases via Command/Query pattern
- Command handlers (write operations)
- Query handlers (read operations)
- DTOs for data transfer

**Infrastructure** (`*/Infrastructure/`)
- Repository implementations (Doctrine)
- ORM mapping (XML/attributes in `Infrastructure/Doctrine/`)
- External integrations (payments, email)
- Framework-specific implementations

**Presentation** (`*/Presentation/`)
- HTTP Controllers (thin, delegate to Application layer)
- Request validation
- Response serialization
- Event listeners for HTTP lifecycle

### Key Architectural Rules

1. **Domain layer is independent** - No Symfony, no Doctrine, pure PHP
2. **Application depends only on Domain**
3. **Infrastructure implements Domain interfaces**
4. **Presentation uses Application and Infrastructure**
5. **Data flow:** Request → Controller → Command/Query → Domain Service → Repository → Database

### Domain Model Design

Domain entities use:
- Readonly classes for immutability
- Named constructors (e.g., `User::register()`)
- Value objects for complex types (Email, Id)
- Private constructors

Infrastructure entities (in `Infrastructure/Doctrine/Entity/`) are separate from Domain entities and handle ORM persistence.

## Multi-Tenancy Model

**Tenant:** Owner (company/organization)
**Scope:** Single database, data isolated by owner_id

Key concepts:
- User can belong to multiple gyms with different roles
- Single authentication (JWT), no re-login when switching gyms
- Gym context passed via `X-Gym-Id` header
- Passes (memberships) are owner-scoped, valid across all owner's gyms
- Roles are relationship-based (via `staff_memberships`, `user_passes`)

## Code Style

Project enforces strict PHP coding standards:
- `declare(strict_types=1)` required on all files
- PHP-CS-Fixer with `@PhpCsFixer` + `@PHP8x` migration rules
- PHPUnit test methods use `snake_case`
- No Yoda conditions
- All code must pass PHPStan level 6

## API Structure

REST API with versioning: `/api/v1/...`

Routes configured via:
- PHP attributes on controllers
- `config/routes.yaml` for module prefixes
- Each module in `*/Presentation/Http/Controller/V1/`

Authentication: JWT via LexikJWTAuthenticationBundle

## Configuration

Service configuration: `config/services.yaml`
- Autowiring and autoconfigure enabled
- Event dispatcher aliased to Symfony adapter
- Custom ExceptionListener for error handling

## Testing

PHPUnit configuration in `phpunit.dist.xml`
- Bootstrap: `tests/bootstrap.php`
- Strict mode (fails on deprecations, notices, warnings)
- Test environment uses `.env.test`

## CI/CD

GitHub Actions workflows (`.github/workflows/ci.yml`):
- PHPUnit tests (PHP 8.5)
- PHPStan analysis (level 6)
- Deptrac architecture layer validation
- PHP-CS-Fixer validation (dry-run with diff)
