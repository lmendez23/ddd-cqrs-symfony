---
inclusion: always
---

# Dragon Ball API - Product Guide

## Project Overview

**Dragon Ball API** is a REST API providing access to Dragon Ball universe characters and planets for external consumers (mobile apps, websites, dashboards). Built with Symfony 7.4 and PHP 8.5 following Hexagonal Architecture + DDD + CQRS patterns.

## Core Domain Contexts

When implementing features, organize code within these bounded contexts:

| Context | Responsibility | Key Entities | Example Use Cases |
|---------|---------------|--------------|-------------------|
| `User` | Authentication & user management | User, RefreshToken | Register, login, JWT generation, password reset |
| `Character` | Dragon Ball character management | Character | CRUD operations, search by name/gender/race/group |
| `Planet` | Dragon Ball planet management | Planet | CRUD operations, search by name/status |

## API Design Standards

### Endpoint Conventions
- **Base URL**: `/api/v1/`
- **Authentication**: JWT Bearer tokens (15min expiry) + refresh tokens (7d expiry)
- **Pagination**: Standard `?page=1&limit=20` (default limit: 20, max: 100)
- **Rate Limiting**: 100 requests/minute per authenticated user
- **Versioning**: URL-based versioning (`/api/v1/`, `/api/v2/`)

### Required Endpoints (MVP)

```
# Authentication
POST /api/v1/auth/register
POST /api/v1/auth/login
POST /api/v1/auth/refresh
POST /api/v1/auth/forgot-password

# Characters
GET    /api/v1/characters
GET    /api/v1/characters/{id}
POST   /api/v1/characters
PUT    /api/v1/characters/{id}
DELETE /api/v1/characters/{id}

# Planets
GET    /api/v1/planets
GET    /api/v1/planets/{id}
POST   /api/v1/planets
PUT    /api/v1/planets/{id}
DELETE /api/v1/planets/{id}
```

### Search Parameters
- **Characters**: `?search=name&gender=male&race=saiyan&group=Z`
- **Planets**: `?search=name&status=active`

## Implementation Guidelines

### Performance Requirements
- **Response Times**: p95 < 150ms for queries, p99 < 200ms
- **Write Operations**: < 500ms
- **Concurrent Users**: Support 1k concurrent requests
- **Availability**: 99.9% uptime target

### Security Standards
- Follow OWASP Top 10 compliance
- JWT tokens with proper expiration
- Input validation on all endpoints
- Rate limiting per user
- Secure password hashing (bcrypt/argon2)

### Data Requirements
- **Characters**: name, gender, race, group, description, image_url
- **Planets**: name, status, description, image_url
- **Users**: email, password, created_at, updated_at
- All entities require UUID primary keys

### Testing Standards
- **Coverage**: >90% Domain/Application layers, >70% Infrastructure
- **Test Types**: Unit tests for domain logic, integration tests for API endpoints
- **Performance**: Test suite must complete in <2 minutes
- **API Documentation**: Auto-generated OpenAPI/Swagger docs

## Development Priorities

### Phase 1 (Current MVP)
1. User authentication system with JWT
2. Character CRUD with search functionality
3. Planet CRUD with search functionality
4. API documentation generation
5. Basic rate limiting and validation

### Phase 2 (Future)
- External API import commands
- Domain events (CharacterCreated, etc.)
- Advanced search filters
- Caching layer implementation

### Explicitly Out of Scope
- Real-time WebSocket features
- Machine learning recommendations
- Frontend/mobile applications
- Legacy data migration tools
- Social features (comments, ratings)

## Quality Gates

Before considering any feature complete:
- [ ] All tests pass with required coverage
- [ ] API endpoints documented in OpenAPI spec
- [ ] Performance requirements met in staging
- [ ] Security review completed
- [ ] Error handling and logging implemented

## Success Metrics
- Developer onboarding: <2 days
- API response time: p95 <150ms in staging
- Error rate: <0.1% in production
- Test coverage: >90% Domain/Application layers
