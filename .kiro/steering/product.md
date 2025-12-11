# Product Overview

This is a Symfony 7.4 web application built with PHP 8.5. The project follows a minimal Symfony setup using the MicroKernelTrait for a lightweight, modern web application architecture.

## Key Characteristics

- **Framework**: Symfony 7.4 with minimal dependencies
- **Runtime**: PHP 8.5 with FPM
- **Architecture**: Microkernel pattern for streamlined application structure
- **Environment**: Dockerized development environment with MySQL database
- **Deployment**: Production-ready with Docker containers and Supervisor process management

The application is designed as a clean, modern Symfony project with standard MVC patterns and follows Symfony best practices for configuration, routing, and dependency injection.

# Product Vision

## Overview

**Dragon Ball API** es una API REST que proporciona acceso a personajes y planetas del universo Dragon Ball para consumidores externos (apps móviles, sitios web, dashboards).

**Usuarios principales**:
- Desarrolladores de apps Dragon Ball (móviles, web)
- Fans que construyen sitios de datos, quizzes, enciclopedias
- Need: Datos estructurados, búsqueda potente, imágenes oficiales, actualizaciones periódicas

## Key Business Goals

- Proporcionar API completa de Dragon Ball con >95% cobertura de personajes/planetas conocidos
- Soporte para 1k concurrent requests con p95 < 150ms
- Fácil integración: JWT auth, paginación estándar, OpenAPI docs auto-generados

## Core Bounded Contexts

| Bounded Context | Responsabilidad Principal | Ejemplos de Casos de Uso |
|-----------------|---------------------------|--------------------------|
| `User`          | Autenticación y gestión de usuarios | Register, login, forgot password, JWT token generation |
| `Character`     | Gestión completa de personajes Dragon Ball | CRUD, search by name/gender/race/group, image + description |
| `Planet`        | Gestión completa de planetas Dragon Ball | CRUD, search by name/status, image + description |

## Functional Requirements (High-Level)

### Must-Have Features
- **Autenticación**: JWT + OAuth2 con refresh tokens [15min expiry, 7d refresh]
- **API REST**: Versionado `/api/v1/`, paginación estándar (`?page=1&limit=20`), rate limiting (100 req/min por user)
- **Búsqueda avanzada**: 
  - Characters: `?name=Vegeta&gender=male&race=saiyan&group=Z`
  - Planets: `?name=Namek&status=active`
- **Multimedia**: URL de imagen oficial + descripción rica por cada entidad

### Non-Functional Requirements
Performance: p99 < 200ms queries, < 500ms writes
Scalability: Soporte para 1k concurrent users
Availability: 99.9% uptime
Data Consistency: Strong consistency para auth, eventual para imports
Security: OWASP Top 10 compliance, secrets en Vault/KMS


## MVP Scope (Minimum Viable Product)

**Fase 1** (primer sprint con Kiro):
✅ User: register, login, forgot password → JWT + refresh token
✅ Character: CRUD personajes, búsqueda por nombre/género/raza/grupo, descripción + imagen URL
✅ Planet: CRUD planetas, búsqueda por nombre/estado, descripción + imagen URL


**Fase 2** (post-MVP):
- ImportCommand: Sincronizar personajes/planetas desde API externa
- Domain events: CharacterCreated → notify external systems

**Acceptance Criteria**:
- 100% coverage en Domain + Application layers
- Tests pasan en < 2min en GitLab CI
- API docs generada automáticamente (OpenAPI/Swagger)
- Logs estructurados JSON con trace_id

## API Endpoints Preview (v1)

```
POST /api/v1/auth/register
POST /api/v1/auth/login
POST /api/v1/auth/forgot-password

GET /api/v1/characters → paginated list
GET /api/v1/characters/{id}
POST /api/v1/characters → create
PUT /api/v1/characters/{id} → update
DELETE /api/v1/characters/{id}
GET /api/v1/characters?search=vegeta&gender=male&race=saiyan

GET /api/v1/planets → paginated list
GET /api/v1/planets/{id}
POST /api/v1/planets
PUT /api/v1/planets/{id}
DELETE /api/v1/planets/{id}
GET /api/v1/planets?search=namek&status=active
```


## Out of Scope (No implementar sin aprobación explícita)

- Import desde API externa (Fase 2)
- Real-time WebSockets
- Machine Learning recommendations
- Mobile app / Frontend
- Legacy data migration

## Success Metrics

- Tiempo de onboarding nuevo developer: < 2 días
- Tiempo respuesta API: p95 < 150ms en staging
- Error rate: < 0.1% en production
- Coverage: > 90% Domain/Application, > 70% Infrastructure

---
*Last updated: 2025-12-11. Review before major features.*
