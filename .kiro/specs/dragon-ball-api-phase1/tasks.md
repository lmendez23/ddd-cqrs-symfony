# Implementation Plan: Dragon Ball API Phase 1

## Overview

This implementation plan breaks down the Dragon Ball API Phase 1 into discrete coding tasks following hexagonal architecture, DDD, and CQRS patterns. Each task builds incrementally, starting with core infrastructure, then implementing each bounded context (User, Character, Planet) with their domain models, application services, and HTTP adapters.

## Tasks

- [ ] 1. Set up project infrastructure and shared components
  - Configure Symfony 7.4 with required bundles (JWT, Doctrine, API Doc, CORS)
  - Set up Docker environment with PHP 8.5, MySQL 8.0, Nginx
  - Configure JWT key generation and authentication
  - Create shared base classes and interfaces
  - _Requirements: 2.5, 11.1, 11.2_

- [ ]* 1.1 Write property test for JWT configuration
  - **Property 7: JWT Security**
  - **Validates: Requirements 2.5, 2.6**

- [ ] 2. Implement User bounded context - Domain layer
  - [ ] 2.1 Create User domain entity with UUID primary key
    - Implement User aggregate with email, password, roles, timestamps
    - Add password encryption and validation methods
    - _Requirements: 1.1, 1.6, 1.7_

  - [ ] 2.2 Create RefreshToken domain entity
    - Implement RefreshToken entity with token, user reference, expiration
    - Add token validation and expiry methods
    - _Requirements: 1.3, 2.2_

  - [ ] 2.3 Define User repository interface
    - Create UserRepositoryInterface in domain layer
    - Define methods for user persistence and retrieval
    - _Requirements: 11.2, 11.3_

  - [ ] 2.4 Define RefreshToken repository interface
    - Create RefreshTokenRepositoryInterface in domain layer
    - Define methods for token persistence and cleanup
    - _Requirements: 11.2, 11.3_

- [ ]* 2.5 Write property tests for User domain
  - **Property 1: User Registration with Encrypted Passwords**
  - **Property 8: UUID Primary Key Invariant**
  - **Validates: Requirements 1.1, 1.6, 3.1, 5.1, 11.1**

- [ ] 3. Implement User bounded context - Application layer
  - [ ] 3.1 Create RegisterUserCommand and Handler
    - Implement command with email, password validation
    - Create handler with user creation and password encryption logic
    - _Requirements: 1.1, 1.7_

  - [ ] 3.2 Create LoginUserCommand and Handler
    - Implement command with credential validation
    - Create handler with JWT and refresh token generation
    - _Requirements: 1.2, 2.1, 2.2_

  - [ ] 3.3 Create RefreshTokenCommand and Handler
    - Implement command with refresh token validation
    - Create handler with new token pair generation
    - _Requirements: 1.3, 2.3_

  - [ ] 3.4 Create ForgotPasswordCommand and Handler
    - Implement command with email validation
    - Create handler with password reset initiation
    - _Requirements: 1.4_

- [ ]* 3.5 Write property tests for User application services
  - **Property 2: JWT Token Generation with Correct Expiry**
  - **Property 3: Token Refresh Round Trip**
  - **Property 4: Password Reset Initiation**
  - **Property 5: Authentication Error Handling**
  - **Validates: Requirements 1.2, 1.3, 1.4, 1.5, 2.1, 2.2, 2.3, 2.4**

- [ ] 4. Implement User bounded context - Infrastructure layer
  - [ ] 4.1 Create Doctrine User entity and repository
    - Implement User Doctrine entity with ORM mapping
    - Create UserRepository implementing domain interface
    - Configure UUID generation and database schema
    - _Requirements: 11.1, 11.4, 11.5_

  - [ ] 4.2 Create Doctrine RefreshToken entity and repository
    - Implement RefreshToken Doctrine entity with ORM mapping
    - Create RefreshTokenRepository implementing domain interface
    - _Requirements: 11.4, 11.5_

  - [ ] 4.3 Create JWT service implementation
    - Implement JWT token generation and validation
    - Configure LexikJWTAuthenticationBundle integration
    - _Requirements: 2.5, 2.6_

  - [ ] 4.4 Create AuthController with authentication endpoints
    - Implement POST /api/v1/auth/register endpoint
    - Implement POST /api/v1/auth/login endpoint
    - Implement POST /api/v1/auth/refresh endpoint
    - Implement POST /api/v1/auth/forgot-password endpoint
    - _Requirements: 12.5_

- [ ]* 4.5 Write property tests for User infrastructure
  - **Property 6: Input Validation**
  - **Property 21: Repository Pattern Compliance**
  - **Validates: Requirements 1.7, 11.2, 11.3, 11.4, 11.5, 11.6**

- [ ] 5. Checkpoint - User context complete
  - Ensure all User context tests pass, ask the user if questions arise.

- [ ] 6. Implement Character bounded context - Domain layer
  - [ ] 6.1 Create Character domain entity
    - Implement Character aggregate with name, gender, race, group, description, image_url
    - Add validation methods for required and optional fields
    - _Requirements: 3.5, 3.6_

  - [ ] 6.2 Define Character repository interface
    - Create CharacterRepositoryInterface in domain layer
    - Define methods for CRUD operations and search
    - _Requirements: 11.2, 11.3_

- [ ]* 6.3 Write property tests for Character domain
  - **Property 11: Character Validation**
  - **Validates: Requirements 3.5, 3.6, 3.7**

- [ ] 7. Implement Character bounded context - Application layer
  - [ ] 7.1 Create CreateCharacterCommand and Handler
    - Implement command with character data validation
    - Create handler with character creation logic
    - _Requirements: 3.1, 3.5_

  - [ ] 7.2 Create UpdateCharacterCommand and Handler
    - Implement command with character update validation
    - Create handler with character modification logic
    - _Requirements: 3.3_

  - [ ] 7.3 Create DeleteCharacterCommand and Handler
    - Implement command with character ID validation
    - Create handler with character removal logic
    - _Requirements: 3.4_

  - [ ] 7.4 Create GetCharacterByIdQuery and Handler
    - Implement query with ID validation
    - Create handler with character retrieval logic
    - _Requirements: 3.2_

  - [ ] 7.5 Create SearchCharactersQuery and Handler
    - Implement query with search and filter parameters
    - Create handler with search logic and pagination
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

- [ ]* 7.6 Write property tests for Character application services
  - **Property 9: Character CRUD Operations**
  - **Property 13: Character Search and Filtering**
  - **Validates: Requirements 3.1, 3.2, 3.3, 3.4, 4.1, 4.2, 4.3, 4.4, 4.5**

- [ ] 8. Implement Character bounded context - Infrastructure layer
  - [ ] 8.1 Create Doctrine Character entity and repository
    - Implement Character Doctrine entity with ORM mapping
    - Create CharacterRepository implementing domain interface
    - Add database indexes for search optimization
    - _Requirements: 11.4, 11.5, 9.5_

  - [ ] 8.2 Create CharacterController with CRUD endpoints
    - Implement GET /api/v1/characters endpoint with search and pagination
    - Implement GET /api/v1/characters/{id} endpoint
    - Implement POST /api/v1/characters endpoint
    - Implement PUT /api/v1/characters/{id} endpoint
    - Implement DELETE /api/v1/characters/{id} endpoint
    - _Requirements: 12.5_

- [ ]* 8.3 Write property tests for Character infrastructure
  - **Property 15: Pagination Limits**
  - **Property 23: Query Optimization**
  - **Validates: Requirements 4.6, 9.5**

- [ ] 9. Implement Planet bounded context - Domain layer
  - [ ] 9.1 Create Planet domain entity
    - Implement Planet aggregate with name, status, description, image_url
    - Add validation methods for required and optional fields
    - _Requirements: 5.5, 5.6_

  - [ ] 9.2 Define Planet repository interface
    - Create PlanetRepositoryInterface in domain layer
    - Define methods for CRUD operations and search
    - _Requirements: 11.2, 11.3_

- [ ]* 9.3 Write property tests for Planet domain
  - **Property 12: Planet Validation**
  - **Validates: Requirements 5.5, 5.6, 5.7**

- [ ] 10. Implement Planet bounded context - Application layer
  - [ ] 10.1 Create CreatePlanetCommand and Handler
    - Implement command with planet data validation
    - Create handler with planet creation logic
    - _Requirements: 5.1, 5.5_

  - [ ] 10.2 Create UpdatePlanetCommand and Handler
    - Implement command with planet update validation
    - Create handler with planet modification logic
    - _Requirements: 5.3_

  - [ ] 10.3 Create DeletePlanetCommand and Handler
    - Implement command with planet ID validation
    - Create handler with planet removal logic
    - _Requirements: 5.4_

  - [ ] 10.4 Create GetPlanetByIdQuery and Handler
    - Implement query with ID validation
    - Create handler with planet retrieval logic
    - _Requirements: 5.2_

  - [ ] 10.5 Create SearchPlanetsQuery and Handler
    - Implement query with search and filter parameters
    - Create handler with search logic and pagination
    - _Requirements: 6.1, 6.2, 6.3, 6.4_

- [ ]* 10.6 Write property tests for Planet application services
  - **Property 10: Planet CRUD Operations**
  - **Property 14: Planet Search and Filtering**
  - **Validates: Requirements 5.1, 5.2, 5.3, 5.4, 6.1, 6.2, 6.3**

- [ ] 11. Implement Planet bounded context - Infrastructure layer
  - [ ] 11.1 Create Doctrine Planet entity and repository
    - Implement Planet Doctrine entity with ORM mapping
    - Create PlanetRepository implementing domain interface
    - Add database indexes for search optimization
    - _Requirements: 11.4, 11.5, 9.5_

  - [ ] 11.2 Create PlanetController with CRUD endpoints
    - Implement GET /api/v1/planets endpoint with search and pagination
    - Implement GET /api/v1/planets/{id} endpoint
    - Implement POST /api/v1/planets endpoint
    - Implement PUT /api/v1/planets/{id} endpoint
    - Implement DELETE /api/v1/planets/{id} endpoint
    - _Requirements: 12.5_

- [ ] 12. Implement global infrastructure components
  - [ ] 12.1 Create global exception handler
    - Implement ExceptionListener for consistent error responses
    - Map domain exceptions to HTTP status codes
    - Add error logging and monitoring
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6_

  - [ ] 12.2 Create rate limiting middleware
    - Implement rate limiting for authenticated users (100 req/min)
    - Add HTTP 429 responses with retry information
    - _Requirements: 8.1, 8.2_

  - [ ] 12.3 Create input validation middleware
    - Implement input sanitization to prevent injection attacks
    - Add comprehensive validation for all API inputs
    - _Requirements: 8.4_

  - [ ] 12.4 Configure API documentation
    - Set up NelmioApiDocBundle for OpenAPI/Swagger generation
    - Add annotations to all controllers for complete documentation
    - Configure interactive documentation interface
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

- [ ]* 12.5 Write property tests for global infrastructure
  - **Property 16: HTTP Response Codes**
  - **Property 17: JSON Response Format**
  - **Property 18: Rate Limiting**
  - **Property 19: Authentication Protection**
  - **Property 20: Input Sanitization**
  - **Property 22: CQRS Pattern Compliance**
  - **Property 24: API Documentation Completeness**
  - **Validates: Requirements 7.1-7.7, 8.1-8.4, 10.2-10.6, 12.1-12.6**

- [ ] 13. Database migrations and schema setup
  - [ ] 13.1 Create database migrations
    - Generate migrations for User, RefreshToken, Character, Planet entities
    - Add proper indexes for search and performance optimization
    - Configure UUID generation at database level
    - _Requirements: 11.1, 9.5_

  - [ ] 13.2 Create database fixtures for development
    - Create sample users, characters, and planets for development
    - Implement fixture loading commands
    - _Requirements: Development support_

- [ ] 14. Final integration and testing
  - [ ] 14.1 Integration testing
    - Test complete API workflows end-to-end
    - Verify authentication flows work across all endpoints
    - Test error handling and edge cases
    - _Requirements: All requirements integration_

  - [ ]* 14.2 Write comprehensive integration tests
    - Test complete user registration and authentication flow
    - Test character and planet management workflows
    - Test search and pagination across all entities
    - _Requirements: All requirements integration_

- [ ] 15. Final checkpoint - Complete system verification
  - Ensure all tests pass, verify API documentation is complete, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Property tests validate universal correctness properties using Eris library
- Unit tests validate specific examples and edge cases using PHPUnit
- Checkpoints ensure incremental validation and user feedback
- All tasks follow hexagonal architecture with proper layer separation
- CQRS pattern is enforced with Commands for writes and Queries for reads
- Repository pattern provides clean abstraction over Doctrine ORM