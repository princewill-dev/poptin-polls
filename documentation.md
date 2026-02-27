# Poptin Polls API Documentation

This document outlines the available API endpoints for the Poptin Polls backend.

**Base URL**: `http://localhost:8005/api` (Local) / `https://your-domain.com/api` (Production)

> [!NOTE]
> The backend is configured to **always return JSON**. You do not need to send the `Accept: application/json` header manually in Postman; the server now handles this automatically for all routes (including 404s).

---

## 🔐 Authentication & Setup

### Check if Admin Exists

- **Endpoint**: `GET /admin-exists`
- **Function**: Checks if any admin user has been created in the system.
- **Auth Required**: No
- **Response**:
  ```json
  { "exists": true }
  ```

### Initial Admin Setup

- **Endpoint**: `POST /admin-setup`
- **Function**: Creates the initial administrator account. Fails if an admin already exists.
- **Auth Required**: No
- **Payload**:
  ```json
  {
    "name": "Admin Name",
    "email": "admin@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }
  ```
- **Response (201 Created)**:
  ```json
  {
    "user": { ... },
    "access_token": "1|abcdef...",
    "token_type": "Bearer",
    "message": "Admin account created successfully"
  }
  ```

### Login

- **Endpoint**: `POST /login`
- **Function**: Authenticates a user or admin.
- **Auth Required**: No
- **Payload**:
  ```json
  {
    "email": "user@example.com",
    "password": "password123"
  }
  ```
- **Response (200 OK)**:
  ```json
  {
    "user": { ... },
    "access_token": "2|ghij...",
    "token_type": "Bearer",
    "message": "Logged in successfully"
  }
  ```

### Register (Voter)

- **Endpoint**: `POST /register`
- **Function**: Registers a new voter account and logs them in.
- **Auth Required**: No
- **Payload**:
  ```json
  {
    "name": "User Name",
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }
  ```
- **Response (201 Created)**:
  ```json
  {
    "user": { ... },
    "access_token": "3|klmn...",
    "token_type": "Bearer",
    "message": "Registered successfully"
  }
  ```

### Logout

- **Endpoint**: `POST /logout`
- **Function**: Terminates the current session.
- **Auth Required**: Yes (Sanctum)
- **Response (200 OK)**:
  ```json
  { "message": "Logged out successfully" }
  ```

### Current User Profile

- **Endpoint**: `GET /user`
- **Function**: Fetches the profile of the currently authenticated user.
- **Auth Required**: Yes (Sanctum)
- **Response (200 OK)**:
  ```json
  {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com",
    "is_admin": true
  }
  ```

---

## 🗳 Public Polls

### Fetch Active Polls

- **Endpoint**: `GET /active-polls`
- **Function**: Retrieves all polls that are currently active (`is_active: true`).
- **Auth Required**: No
- **Response (200 OK)**:
  ```json
  [
    {
      "id": 1,
      "question": "What is your favorite color?",
      "slug": "12345678",
      "options": [
        { "id": 1, "text": "Red", "votes_count": 5 },
        { "id": 2, "text": "Blue", "votes_count": 3 }
      ]
    }
  ]
  ```

### View Single Poll

- **Endpoint**: `GET /polls/{slug}`
- **Function**: Fetches details for a specific poll by its 8-digit slug.
- **Auth Required**: No
- **Response (200 OK)**:
  ```json
  {
    "id": 1,
    "question": "Question text?",
    "slug": "12345678",
    "options": [...],
    "has_voted": true
  }
  ```

### Cast a Vote

- **Endpoint**: `POST /polls/{slug}/vote`
- **Function**: Submits a vote for a poll.
- **Auth Required**: No
- **Payload**:
  ```json
  {
    "option_id": 5
  }
  ```

* **Response (200 OK)**:
  ```json
  {
    "message": "Vote recorded successfully."
  }
  ```
* **Response (422 Unprocessable Content)**:
  ```json
  {
    "message": "You have already voted on this poll.",
    "errors": {
      "vote": ["You have already voted on this poll."]
    }
  }
  ```

---

## 🛠 Admin Management

_All endpoints below require an authenticated session and `is_admin: true` permissions._

### List All Polls

- **Endpoint**: `GET /polls`
- **Function**: Retrieves all polls with full relationship data (options, detailed votes).
- **Response (200 OK)**: Array of complete poll objects.

### Create New Poll

- **Endpoint**: `POST /polls`
- **Function**: Creates a new poll with options.
- **Payload**:
  ```json
  {
    "question": "New Question?",
    "options": ["Option A", "Option B", "Option C"]
  }
  ```
- **Response (201 Created)**: The newly created poll object with its options and generated slug.

### View Detailed Admin Analytics

- **Endpoint**: `GET /polls/{slug}/admin`
- **Function**: Fetches comprehensive data for a poll, including voter names and specific choices.
- **Response (200 OK)**: Poll object with `votes` relationship loaded including user and option details.

### Update Poll

- **Endpoint**: `PUT /polls/{slug}`
- **Function**: Updates the poll question and manages options (modify, add, or remove).
- **Payload**:
  ```json
  {
    "question": "Updated Question?",
    "options": [
      { "id": 1, "text": "Existing Option Updated" },
      { "id": null, "text": "New Option being added" }
    ]
  }
  ```
- **Response (200 OK)**: Updated poll object.

### Delete Poll

- **Endpoint**: `DELETE /polls/{slug}`
- **Function**: Permanently removes a poll and all associated votes.
- **Response (200 OK)**:
  ```json
  { "message": "Poll deleted successfully" }
  ```

### Toggle Poll Status

- **Endpoint**: `PATCH /polls/{slug}/toggle-status`
- **Function**: Pauses or resumes a poll (switches `is_active` boolean).
- **Response (200 OK)**: Updated poll object reflecting the status change.
