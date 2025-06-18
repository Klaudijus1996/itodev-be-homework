# BE-FE homework

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

### Frontend (React + TypeScript):
- Implement application state management using Redux Toolkit, React Context API or other state state management tool.
- Consume the Symfony API to:
  - Display a list of available events.
  - Submit event registration forms.
  - Use a modern UI framework (e.g., MUI, Tailwind, Chakra) to build a custom, visually appealing, responsive UI.
  - Ensure good user experience across devices and accessibility standards.

---

Groom

BE

- Init new symphony + MySQL project
- Define Event model with name: string, date: date, location: string, available_spots: int, timestamps
    - Optionally could make a status functionality
- Define EventRegistration model with: name: string, email: string, timestamps
- Define events.v1.index, events.v1.register routes
- Define http validation for:
    - more than 1 event can't registered at the same date, same place (unique event_date_event_location) (optional)
    - user cant register to an event, if it's already registered for that event (event_id_event_registration_email unique)
    - unique constraints should only be in the context of API & not in DB.
- 200, 422 codes

FE

Init new repo
Define axios client with few endpoints:
- List events
- Registered to an event
  Define a paginated table list for simple events display
  Define a combination of dialog & drawer for a registration to an event form.
