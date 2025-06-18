# BE homework

### Build a Simple Event Registration System

#### Description: Develop a simple system where users can register for events. The backend should expose RESTful API endpoints using Symfony. The frontend should consume these APIs using React with TypeScript. The UI should be styled with a modern design framework and provide a polished, user-friendly experience.

### Requirements:
- Backend (Symfony):
  - Create an “Event” entity with fields like name, date, location, and available spots.
  - Create API endpoints to:
    - Retrieve a list of available events.
    - Submit a registration for an event (name, email, etc.).
    - Validate inputs (e.g., required fields, email format, check for available spots).
    - Store data in the database.
    - Return structured JSON responses with proper status codes and error handling.
