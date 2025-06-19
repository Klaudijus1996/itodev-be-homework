# Event Registration System

A simple system where users can register for events. The backend exposes RESTful API endpoints using Symfony.

## Overview

This project is a Symfony-based backend for an event registration system. It allows users to:
- View a list of available events
- Register for events by providing their name and email

The system ensures that:
- Users cannot register for an event if there are no available spots
- Users cannot register for the same event multiple times with the same email

## Technologies

- PHP 8.2+
- Symfony 7.3
- Doctrine ORM
- MySQL 8
- FrankenPHP (for serving the application)
- Docker & Docker Compose (for development environment)

## Setup

### Prerequisites

- Docker and Docker Compose installed on your system
- Git

### Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd itodev-be-homework
   ```

2. Start the Docker containers:
   ```bash
   make start
   ```

   This will build and start the necessary containers (PHP with FrankenPHP and MySQL).

3. Install dependencies:
   ```bash
   make composer c="install"
   ```

4. Create the database schema:
   ```bash
   make sf c="doctrine:migrations:migrate --no-interaction"
   ```

5. (Optional) Load fixtures to have some initial data:
   ```bash
   make sf c="doctrine:fixtures:load --no-interaction"
   ```

6. The application should now be running at:
   - HTTP: http://localhost:80
   - HTTPS: https://localhost:443

## API Documentation

### Endpoints

#### List Events

```
GET /api/v1/events
```

Query parameters:
- `page` (optional): Page number for pagination (default: 1)
- `limit` (optional): Number of items per page (default: 10)

Response:
```json
{
  "items": [
    {
      "id": 123456789,
      "name": "Event Name",
      "date": "2023-12-31",
      "location": "Event Location",
      "available_spots": 100,
      "created_at": "2023-01-01T12:00:00+00:00"
    }
  ],
  "meta": {
    "page": 1,
    "limit": 10,
    "total": 1
  }
}
```

#### Register for an Event

```
POST /api/v1/events/{id}/register
```

Request body:
```json
{
  "name": "John Doe",
  "email": "john.doe@example.com"
}
```

Success Response (201 Created):
```json
{
  "id": 987654321,
  "name": "John Doe",
  "email": "john.doe@example.com",
  "event": {
      "id": 123456789,
      "name": "Event Name",
      "date": "2023-12-31",
      "location": "Event Location",
      "available_spots": 100,
      "created_at": "2023-01-01T12:00:00+00:00"
  },
  "created_at": "2023-01-01T12:00:00+00:00"
}
```

Error Responses:
- 404 Not Found: Event not found
- 409 Conflict: No spots available or already registered

## Development

### Useful Commands

The project includes a Makefile with several useful commands:

- `make help`: Show available commands
- `make start`: Build and start the containers
- `make up`: Start the containers
- `make down`: Stop the containers
- `make logs`: Show container logs
- `make sh`: Connect to the PHP container
- `make bash`: Connect to the PHP container with bash
- `make composer c="<command>"`: Run a Composer command
- `make sf c="<command>"`: Run a Symfony command
- `make cc`: Clear the Symfony cache
- `make test [c="<options>"]`: Run tests
- `make phpstan [p="<options>"]`: Run phpstan static analysis

### Project Structure

- `src/Controller/Api/V1/`: API controllers
- `src/Entity/`: Doctrine entities
- `src/Repository/`: Doctrine repositories
- `src/Dto/`: Data Transfer Objects for API requests and responses
- `migrations/`: Database migrations
- `tests/`: Test files

## Testing

Run the tests with:

```bash
make test
```

To run specific tests or with specific options:

```bash
make test c="--group=api"
```

## Future Improvements

Potential enhancements for the project:

- Implement transactional API middleware for data consistency
- Add database locks to prevent race conditions during registration
- Enhance API documentation using API Platform
- Implement more sophisticated sorting and filtering for the events endpoint
- Add more comprehensive test coverage
