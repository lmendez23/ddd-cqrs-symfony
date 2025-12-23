---
inclusion: always
---

# Technology Stack & Development Guidelines

## Core Technology Requirements

- **PHP**: 8.5+ (minimum 8.2) - Use strict types (`declare(strict_types=1);`)
- **Framework**: Symfony 7.4 - Follow Symfony best practices and conventions
- **Database**: MySQL 8.0.43 - Use Doctrine ORM exclusively
- **Web Server**: Nginx (latest) - Configured for PHP-FPM
- **Process Manager**: PHP-FPM with Supervisor for background tasks

## Required Dependencies

### Core Symfony
- `symfony/framework-bundle`: Core framework functionality
- `symfony/console`: CLI commands (use for data imports, maintenance)
- `symfony/dotenv`: Environment management
- `symfony/flex`: Recipe system for auto-configuration
- `symfony/runtime`: Modern application bootstrap
- `symfony/yaml`: Configuration files

### Authentication & Security
- `symfony/security-bundle`: Security layer
- `lexik/jwt-authentication-bundle`: JWT token handling
- **Rule**: Always use JWT for API authentication, 60min expiry + refresh tokens

### Database & ORM
- `doctrine/orm`: Object-relational mapping
- `doctrine/doctrine-bundle`: Symfony integration
- `doctrine/doctrine-migrations-bundle`: Database versioning
- **Rule**: Use UUIDs for all entity primary keys, never expose auto-increment IDs

### API Development
- `symfony/serializer`: JSON serialization/deserialization
- `nelmio/api-doc-bundle`: OpenAPI/Swagger documentation generation
- `nelmio/cors-bundle`: Cross-origin resource sharing
- **Rule**: All API endpoints must be documented with OpenAPI annotations

### Validation & Testing
- `symfony/validator`: Input validation (use on all API inputs)
- `phpunit/phpunit`: Unit and integration testing
- `doctrine/doctrine-fixtures-bundle`: Test data fixtures

## Development Environment

### Docker Configuration
- **App Container**: PHP 8.5-FPM with Composer
- **Web Server**: Nginx on port 8060
- **Database**: MySQL on port 6306
- **Process Management**: Supervisor for background processes

### Essential Commands

```bash
# Environment setup
docker-compose up -d
composer install

# Cache management (run after config changes)
bin/console cache:clear
bin/console cache:warmup

# Development tools
bin/console debug:router          # List all routes
bin/console debug:container       # Service container debug
bin/console debug:autowiring      # Check service autowiring

# Database operations
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate
bin/console doctrine:fixtures:load --env=dev

# JWT configuration (run once)
bin/console lexik:jwt:generate-keypair

# Testing
bin/console --env=test doctrine:database:create
bin/console --env=test doctrine:migrations:migrate
vendor/bin/phpunit

# Application access
# Local: http://localhost:8060
```

### Environment Configuration Rules
- **`.env`**: Default values, committed to repository
- **`.env.local`**: Local overrides, never commit
- **`.env.test`**: Test environment specific values
- **Rule**: Never commit sensitive data (passwords, keys) to `.env`

## Code Standards & Conventions

### PHP Standards
- **PSR-4**: Autoloading (namespace `App\`)
- **PSR-12**: Coding style
- **Strict Types**: Always use `declare(strict_types=1);`
- **Type Hints**: Use return types and parameter types on all methods

### Symfony Conventions
- **Route Attributes**: Use `#[Route]` on controller methods (preferred over YAML)
- **Service Autowiring**: Enabled by default for `src/` directory
- **Auto-discovery**: Services automatically registered from `src/`
- **Configuration**: Use YAML for config files, PHP attributes for routes/validation

### API Response Standards
- **Success**: HTTP 200/201 with JSON response
- **Errors**: Proper HTTP status codes (400, 401, 403, 404, 422, 500)
- **Pagination**: Use `?page=1&limit=20` (default limit: 20, max: 100)
- **Rate Limiting**: 100 requests/minute per authenticated user

### Performance Guidelines
- **Response Times**: Target p95 < 150ms for queries
- **Database**: Use Doctrine Query Builder, avoid raw SQL
- **Caching**: Implement for frequently accessed data
- **Validation**: Validate all inputs at controller level

## Build & Deployment Process

### Symfony Flex Integration
- Automatic bundle configuration via recipes
- Service auto-discovery in `src/` directory
- Automatic route registration via PHP attributes
- Environment-based configuration loading

### Quality Assurance
- **Testing**: >90% coverage for Domain/Application layers
- **Static Analysis**: Use PHPStan or Psalm for type checking
- **Code Style**: Follow PSR-12, use PHP-CS-Fixer
- **Documentation**: Auto-generate API docs with Swagger annotations
