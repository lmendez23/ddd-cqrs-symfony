# Project Structure

## Root Directory Layout

```
├── bin/                    # Executable scripts (console commands)
├── config/                 # Application configuration
│   ├── packages/           # Bundle-specific configuration
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

## Hexagonal Architecture Rules

To enforce Hexagonal Architecture, the following dependency rules apply:  

- **Domain layer**:
  - Must not depend on Symfony, Doctrine, HTTP, or any infrastructure library.
  - Contains only domain models (`Model`), domain events and domain contracts (interfaces in `Contracts`).  
- **Application layer**:
  - Coordinates use cases using domain contracts and models.
  - Depends on `Domain` but never on concrete infrastructure implementations.
  - Contains:
    - `Command/`: write operations (create, update, delete).
    - `Query/`: read operations focused on returning data.
    - `Event/`: application-level events if needed.  
- **Infrastructure layer**:
  - Implements the contracts defined in `Domain` and `Application`.
  - Can depend on Symfony, Doctrine, Guzzle, Messenger, etc.
  - Maps the outside world (HTTP, DB, queues) to the inner layers.

Data flow should follow:

> HTTP / CLI / Message → Infrastructure (Symfony controller / command / consumer) → Application (Command/Query handler) → Domain (models, domain services, repositories via interfaces) → back through Infrastructure adapters.  

## CQRS Conventions

The project uses CQRS to clearly split write and read responsibilities:  

- **Commands**:
  - Represent intent to change system state.
  - Live in `<ContextName>\Application\Command`.
  - Are handled by dedicated handlers (for example, `FooCommandHandler`) that:
    - Validate intent at the application level.
    - Call domain logic via aggregates and domain services.
    - Persist changes using repository contracts.  
- **Queries**:
  - Represent read operations, optimized for returning data.
  - Live in `<ContextName>\Application\Query`.
  - Are handled by `QueryHandler` classes that:
    - Do not change state.
    - May read from optimized views or projections if required.  
- **Controllers / Adapters**:
  - Live under `<ContextName>\Infrastructure\Symfony\Controller`.
  - For write endpoints:
    - Build a Command from the HTTP request.
    - Dispatch the Command to its handler (for example, via Symfony Messenger or direct service call).
  - For read endpoints:
    - Build a Query from request parameters.
    - Invoke the appropriate QueryHandler and return a serialized response.

### Configuration Structure
- **`config/packages/`**: Framework and bundle configurations
- **`config/routes/`**: Route definitions (if not using attributes)
- **`config/services.yaml`**: Main service container configuration
  - Service registration and autowiring rules.
  - Ensures handlers and controllers under the `src/` structure are auto-discovered as services.
  - Can be used to enforce layer boundaries (for example, by service naming or tags).
- **Auto-configuration**: `_defaults` should be set so that:
    - Controllers in `Infrastructure\Symfony\Controller` are automatically tagged as `controller.service_arguments`.  
    - Command and Query handlers in `Application` are autowired and optionally tagged for Messenger if used.

## Key Conventions

### File Naming
- Controllers: `SomethingController.php`
- Commands / Queries: Verb-based names like `CreateOrderCommand.php`, `GetOrderByIdQuery.php`.  
- Handlers: Match their command or query, for example `CreateOrderCommandHandler.php`.  
- Domain models: Ubiquitous language names, for example `Order.php`, `OrderLine.php`. 
- Configuration: YAML format preferred

### Namespace Structure
- Root namespace: `App\`.  
- Shareable code: `App\Shared\...`.  
- Bounded context: `App\<ContextName>\Domain`, `App\<ContextName>\Application`, `App\<ContextName>\Infrastructure`.

### Environment Files
- `.env`: Committed defaults
- `.env.local`: Local overrides (gitignored)
- `.env.{environment}`: Environment-specific settings (for example, `.env.prod`).

## Docker Structure

- **Multi-stage builds** to keep production images small.  
- **Volume mounts** for local development so that code changes are reflected immediately.  
- **Health checks** to monitor the state of PHP-FPM, web server and dependencies.  
- Optional process **supervisor** if background workers (queue consumers, schedulers) are needed.