---
inclusion: always
---

# Project Structure & Architecture Guide

## CRITICAL RULES FOR AI ASSISTANT

### File Placement Rules
- **NEVER** place files outside their designated layer boundaries
- **Domain layer**: No Symfony, Doctrine, or infrastructure dependencies
- **Application layer**: Only domain contracts, never concrete infrastructure
- **Infrastructure layer**: Framework-specific implementations only

### Bounded Context Rules
- Use existing contexts: `User`, `Character`, `Planet` (from product.md)
- Create new files within appropriate context: `src/{Context}/{Layer}/{Type}/`
- Shared code goes in `src/Shared/` only if used by multiple contexts

### Naming Conventions (ENFORCE STRICTLY)
- Commands: `{Verb}{Entity}Command.php` (e.g., `CreateCharacterCommand.php`)
- Queries: `{Verb}{Entity}Query.php` (e.g., `GetCharacterByIdQuery.php`)
- Handlers: `{CommandOrQuery}Handler.php` (e.g., `CreateCharacterCommandHandler.php`)
- Controllers: `{Entity}Controller.php` (e.g., `CharacterController.php`)
- Domain Models: Entity names (e.g., `Character.php`, `Planet.php`)

## Root Directory Layout

```
├── bin/                    # Executable scripts (console commands)
├── config/                 # Application configuration
│   ├── jwt/                # JWT keys
│   ├── packages/           # Bundle-specific configuration
│   │   ├── doctrine.yaml
│   │   ├── security.yaml
│   │   └── nelmio_api_doc.yaml
│   ├── routes/             # Route definitions
│   ├── bundles.php         # Bundle registration
│   ├── services.yaml       # Service container configuration
│   └── routes.yaml         # Main routing configuration
├── docker/                 # Docker configuration
│   ├── nginx/              # Nginx web server config
│   └── php/                # PHP-FPM container setup
├── public/                 # Web-accessible files
│   └── index.php           # Application entry point
├── src/                    # Application source code
│   ├── Shared/             # Shared kernel (cross-cutting domain & infra)
│   │   ├── Application/
│   │   │   ├── Command/    # Shared commands
│   │   │   ├── Event/      # Shared application events
│   │   │   └── Query/      # Shared queries
│   │   ├── Domain/
│   │   │   ├── Contracts/  # Shared domain contracts (interfaces, abstractions)
│   │   │   ├── Event/      # Shared domain events
│   │   │   └── Model/      # Shared entities, value objects, aggregates
│   │   └── Infrastructure/
│   │       ├── Doctrine/   # Shared persistence adapters (ORM, DB)
│   │       ├── Guzzle/     # Shared HTTP client adapters
│   │       ├── Rest/       # Shared REST-related infrastructure
│   │       └── Symfony/
│   │           ├── Command/    # Shared Symfony console commands
│   │           ├── Controller/ # Shared Symfony HTTP controllers / endpoints
│   │           ├── Http/       # Shared HTTP concerns (request/response helpers, middleware)
│   │           └── Model/      # DTOs / view models used at the edges
│   ├── <ContextName>/      # Bounded context (per domain area)
│   │   ├── Application/
│   │   │   ├── Command/    # Commands (write side use cases)
│   │   │   ├── Event/      # Application events in this context
│   │   │   └── Query/      # Queries (read side use cases)
│   │   ├── Domain/
│   │   │   ├── Contracts/  # Domain interfaces (repositories, services, buses)
│   │   │   ├── Event/      # Domain events for this context
│   │   │   └── Model/      # Aggregates, entities, value objects
│   │   └── Infrastructure/
│   │       ├── Doctrine/   # Doctrine entities, repositories, DB mappings
│   │       ├── Guzzle/     # Integrations with external HTTP APIs
│   │       ├── Rest/       # REST API infrastructure specific to this context
│   │       └── Symfony/
│   │           ├── Command/    # Console commands for this context
│   │           ├── Controller/ # HTTP controllers for this context (adapters)
│   │           ├── Http/       # HTTP-specific helpers / request handling
│   │           └── Model/      # Input/Output models (DTOs) for controllers
│   └── Kernel.php          # Application kernel
├── var/                    # Runtime files (cache, logs)
│   ├── cache/              # Application cache
│   └── log/                # Application logs
└── vendor/                 # Composer dependencies

```

## Source Code Organization (Hexagonal + DDD + CQRS)

### `src/` Directory
- **Namespace**: `App\`
- **PSR-4 Autoloading**: All classes follow PSR-4 standards
- **Service Auto-discovery**: Classes automatically registered as services
- **Controller**: HTTP request handlers with route attributes
- **Bounded contexts**: Each business area lives under `App\<ContextName>\...`, following DDD
- **Hexagonal layering**:
    - `Domain`: Pure domain model and contracts, with no framework dependencies.
    - `Application`: Use cases (commands/queries) and their orchestration.
    - `Infrastructure`: Adapters for persistence, HTTP, messaging, and framework-specific details.

### Shared Kernel (`src/Shared`)

The `Shared` namespace contains concepts and infrastructure reused across multiple bounded contexts:  

- `Shared\Domain`:
  - Common value objects, base entities, domain events and shared domain services.
  - Contracts that are reused across contexts (for example, a shared event bus interface).  
- `Shared\Application`:
  - Cross-cutting commands, queries and application-level events.
  - Base classes or traits for handlers if needed.  
- `Shared\Infrastructure`:
  - Technical adapters like shared Doctrine types, base repositories, HTTP clients (Guzzle) and common Symfony controllers or middleware.

## Hexagonal Architecture - Layer Dependencies (ENFORCE)

**STRICT DEPENDENCY RULES:**

### Domain Layer (`src/{Context}/Domain/`)
- **ALLOWED**: Only PHP standard library, domain contracts within same context
- **FORBIDDEN**: Symfony, Doctrine, HTTP, any framework dependencies
- **CONTAINS**: Entities, Value Objects, Domain Events, Repository Interfaces
- **EXAMPLE**: `src/Character/Domain/Model/Character.php`

### Application Layer (`src/{Context}/Application/`)
- **ALLOWED**: Domain layer, application contracts
- **FORBIDDEN**: Concrete infrastructure implementations (Doctrine entities, HTTP requests)
- **CONTAINS**: Command/Query handlers, Application services
- **EXAMPLE**: `src/Character/Application/Command/CreateCharacterCommandHandler.php`

### Infrastructure Layer (`src/{Context}/Infrastructure/`)
- **ALLOWED**: All layers, framework dependencies
- **CONTAINS**: Controllers, Doctrine entities, Repository implementations
- **EXAMPLE**: `src/Character/Infrastructure/Symfony/Controller/CharacterController.php`

**Data Flow (ALWAYS FOLLOW):**
```
HTTP Request → Controller → Command/Query Handler → Domain Service → Repository Interface → Repository Implementation
```  

## CQRS Implementation Rules

### Commands (Write Operations)
- **Location**: `src/{Context}/Application/Command/`
- **Purpose**: Change system state (Create, Update, Delete)
- **Naming**: `{Verb}{Entity}Command.php`
- **Handler**: `{Command}Handler.php` in same directory
- **Example**: 
  ```php
  // src/Character/Application/Command/CreateCharacterCommand.php
  // src/Character/Application/Command/CreateCharacterCommandHandler.php
  ```

### Queries (Read Operations)
- **Location**: `src/{Context}/Application/Query/`
- **Purpose**: Retrieve data without state changes
- **Naming**: `{Action}{Entity}Query.php`
- **Handler**: `{Query}Handler.php` in same directory
- **Example**:
  ```php
  // src/Character/Application/Query/GetCharacterByIdQuery.php
  // src/Character/Application/Query/GetCharacterByIdQueryHandler.php
  ```

### Controllers (HTTP Adapters)
- **Location**: `src/{Context}/Infrastructure/Symfony/Controller/`
- **Responsibility**: Convert HTTP requests to Commands/Queries
- **Pattern**: 
  - Build Command/Query from request
  - Dispatch to handler
  - Return HTTP response
- **Example**: `src/Character/Infrastructure/Symfony/Controller/CharacterController.php`

## File Creation Guidelines

### When Creating New Features
1. **Identify Context**: Use `User`, `Character`, or `Planet` (or create new if justified)
2. **Choose Layer**: Domain → Application → Infrastructure (inside-out approach)
3. **Follow Structure**: Always use the prescribed directory structure
4. **Check Dependencies**: Ensure no layer violations

### Required File Patterns
- **Domain Entity**: `src/{Context}/Domain/Model/{Entity}.php`
- **Repository Interface**: `src/{Context}/Domain/Contracts/{Entity}RepositoryInterface.php`
- **Command**: `src/{Context}/Application/Command/{Verb}{Entity}Command.php`
- **Command Handler**: `src/{Context}/Application/Command/{Verb}{Entity}CommandHandler.php`
- **Query**: `src/{Context}/Application/Query/{Action}{Entity}Query.php`
- **Query Handler**: `src/{Context}/Application/Query/{Action}{Entity}QueryHandler.php`
- **Controller**: `src/{Context}/Infrastructure/Symfony/Controller/{Entity}Controller.php`
- **Doctrine Entity**: `src/{Context}/Infrastructure/Doctrine/Entity/{Entity}.php`
- **Repository Implementation**: `src/{Context}/Infrastructure/Doctrine/Repository/{Entity}Repository.php`

### Namespace Rules
- **Root**: `App\`
- **Context**: `App\{Context}\`
- **Layer**: `App\{Context}\{Layer}\`
- **Type**: `App\{Context}\{Layer}\{Type}\`

### Configuration Files
- **Services**: Auto-discovery enabled for `src/` directory
- **Routes**: Use PHP attributes on controllers (preferred) or `config/routes/`
- **Doctrine**: Mapping in `src/{Context}/Infrastructure/Doctrine/Mapping/`

## Code Quality Rules

### PSR Standards
- **PSR-4**: Autoloading (strictly enforced)
- **PSR-12**: Coding style
- **Type Declarations**: Always use strict types (`declare(strict_types=1);`)

### Symfony Conventions
- **Route Attributes**: Use `#[Route]` on controller methods
- **Service Autowiring**: Enabled by default for `src/` directory
- **Environment Variables**: Use `.env` files, access via `$_ENV` or DI

### Testing Structure
- **Unit Tests**: `tests/Unit/{Context}/{Layer}/`
- **Integration Tests**: `tests/Integration/{Context}/`
- **Coverage**: >90% for Domain/Application layers

## Common Mistakes to Avoid

1. **Layer Violations**: Never import Doctrine entities in Domain layer
2. **Wrong Placement**: Don't put business logic in controllers
3. **Naming Inconsistency**: Always follow the prescribed naming patterns
4. **Missing Interfaces**: Always define repository interfaces in Domain layer
5. **Direct DB Access**: Always use repository pattern, never direct Doctrine queries in controllers