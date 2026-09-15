# StockCore

**StockCore** is a production-oriented REST API for inventory, sales, and order
management, built with Laravel.

The system is designed to manage the complete lifecycle of products and stock:
from suppliers and inventory movements to customer orders, stock validation,
and order processing.

Rather than being a simple CRUD API, StockCore focuses on solving common
backend challenges found in real-world inventory systems, including transactional
consistency, concurrent stock updates, role-based access control, asynchronous
processing, caching, API security, automated testing, and observability.

## Core Domain

StockCore will provide management for:

- Products and categories
- Suppliers
- Customers
- Inventory
- Stock movements
- Orders and order items
- Users, roles, and permissions
- Sales reports

## Engineering Goals

The project is being built as a production-oriented Laravel backend with emphasis on:

- RESTful API design and versioning
- Authentication and authorization
- Database transactions and data consistency
- Concurrency control for inventory operations
- Role-based access control (RBAC)
- Asynchronous jobs and queues
- Redis caching
- Structured error handling
- Filtering, sorting, and pagination
- Automated testing
- Static analysis and code quality
- API documentation
- Containerized development
- Continuous Integration and deployment

## Current Stack

- PHP 8.5
- Laravel 13
- PHPUnit

## Planned Architecture & Tooling

### Application

- Laravel REST API
- Laravel Sanctum
- Laravel Policies
- Spatie Laravel Permission

### Data

- PostgreSQL
- Redis

### Asynchronous Processing

- Laravel Queues
- Laravel Horizon

### Development & Observability

- Laravel Telescope

### Quality

- PHPUnit
- Laravel Pint
- Larastan / PHPStan

### API Documentation

- OpenAPI
- Postman Collection

### Infrastructure

- Docker
- Docker Compose
- GitHub Actions
- CI/CD

## API

The API will be versioned from its first public endpoints:

`/api/v1`

## Project Status

🚧 **Under active development**

StockCore is being developed incrementally. Technologies listed under planned
architecture and tooling will be incorporated only when required by an implemented
use case.
