# Technology Stack

## Core Technologies

- **PHP**: 8.5+ (minimum 8.2)
- **Framework**: Symfony 7.4
- **Database**: MySQL 8.0.43
- **Web Server**: Nginx (latest)
- **Process Manager**: PHP-FPM with Supervisor

## Key Dependencies

- `symfony/framework-bundle`: Core Symfony framework
- `symfony/console`: CLI command support
- `symfony/dotenv`: Environment variable management
- `symfony/flex`: Symfony recipe system
- `symfony/runtime`: Modern application runtime
- `symfony/yaml`: YAML configuration support

## Development Environment

### Docker Setup
- **App Container**: PHP 8.5-FPM with Composer
- **Web Server**: Nginx on port 8060
- **Database**: MySQL on port 6306
- **Process Management**: Supervisor for background processes

### Common Commands

```bash
# Start development environment
docker-compose up -d

# Install dependencies
composer install

# Clear cache
bin/console cache:clear

# Debug routes
bin/console debug:router

# Run console commands
bin/console [command]

# Access application
http://localhost:8060
```

### Environment Configuration
- `.env`: Default environment variables
- `.env.local`: Local overrides (not committed)
- Environment-specific files: `.env.dev`, `.env.prod`

## Build Process

The application uses Symfony Flex for automatic configuration and follows standard Symfony conventions:
- Auto-discovery of services in `src/`
- Automatic route registration via attributes
- PSR-4 autoloading with `App\` namespace