# Voting System

A web-based voting system that allows administrators to manage voting sessions and students to participate in assigned voting sessions.

## System Architecture

### Frontend
- HTML5, CSS3 (Bootstrap 5)
- JavaScript (Vanilla JS)
- Single Page Application (SPA) style navigation
- Responsive design for mobile and desktop

### Backend
- PHP (API-based architecture)
- MySQL Database
- RESTful API endpoints

### Security Features
- Session-based authentication
- Role-based access control (Admin/Student)
- Secure password handling
- Protected API endpoints

## Architectural Patterns & Design Principles

### 1. Client-Server Architecture
- Clear separation between frontend (client) and backend (server)
- RESTful API communication
- Stateless server operations
- Client-side state management using appState object

### 2. MVC-like Pattern
- **Models**: PHP classes handling data operations (users, sessions, votes)
- **Views**: HTML files with Bootstrap for UI presentation
- **Controllers**: PHP API endpoints handling business logic

### 3. Repository Pattern
- Centralized database operations
- Abstracted data access layer in `db.php`
- Consistent data handling across endpoints

### 4. Module Pattern (JavaScript)
- IIFE (Immediately Invoked Function Expression) for encapsulation
- Private state management using closures
- Modular function organization

### 5. Service Layer Pattern
- Separate API services for different functionalities
  - Authentication Service (login, logout)
  - Session Service (session management)
  - Voting Service (vote handling)
  - Results Service (result aggregation)

### 6. Single Responsibility Principle
- Each API endpoint handles one specific functionality
- Separate HTML files for different features
- Modular CSS organization

### 7. State Management
- Centralized state handling using appState object
- Event-driven updates
- Consistent data flow

### 8. Factory Pattern
- Dynamic UI component creation
- Standardized error handling
- Reusable form generation

### 9. Observer Pattern
- Event listeners for UI updates
- Real-time form validation
- Dynamic content loading

### 10. Security Patterns
- Authentication middleware
- Input validation layers
- Output sanitization
- Prepared statements for SQL

## Core Features

### Admin Functions
1. **Session Management**
   - Create new voting sessions
   - Close active sessions
   - View all session results
   - Assign sessions to students

2. **Candidate Management**
   - Add/Remove candidates
   - Manage candidate information

3. **User Management**
   - View registered students
   - Manage session assignments

### Student Functions
1. **Voting**
   - View assigned active sessions
   - Cast votes in assigned sessions
   - View results of closed sessions

2. **Results**
   - View results of assigned closed sessions
   - See vote counts and percentages

## Database Structure

### Tables
1. **users**
   - id (Primary Key)
   - username
   - password (hashed)
   - role (admin/student)

2. **sessions**
   - id (Primary Key)
   - name
   - status (open/closed)
   - created_at

3. **session_users**
   - session_id (Foreign Key)
   - user_id (Foreign Key)

4. **candidates**
   - id (Primary Key)
   - name
   - session_id (Foreign Key)

5. **votes**
   - id (Primary Key)
   - session_id (Foreign Key)
   - candidate_id (Foreign Key)
   - user_id (Foreign Key)

## API Endpoints

### Authentication
- `login.php`: User authentication
- `logout.php`: Session termination
- `register.php`: New user registration

### Session Management
- `sessions.php`: CRUD operations for voting sessions
- `session_assign.php`: Manage student session assignments

### Voting Operations
- `candidates.php`: Manage candidates
- `vote.php`: Handle vote casting
- `results.php`: Retrieve voting results

### User Information
- `user_info.php`: Get current user details

## File Structure
```
├── assets/                      # Static resources
│   ├── css/                    # Styling
│   │   └── style.css          # Global styles
│   └── js/                     # Client-side JavaScript modules
│       ├── state.js           # State management module
│       ├── api.js             # API service module
│       ├── ui.js              # UI components factory
│       └── validators.js      # Form validation module
├── php/
│   ├── api/                   # RESTful API endpoints (Controllers)
│   │   ├── candidates.php     # Candidate management
│   │   ├── login.php         # Authentication
│   │   ├── logout.php        # Session termination
│   │   ├── register.php      # User registration
│   │   ├── results.php       # Results aggregation
│   │   ├── session_assign.php # Session assignment
│   │   ├── sessions.php      # Session management
│   │   ├── user_info.php     # User information
│   │   └── vote.php          # Vote handling
│   ├── includes/             # Shared components
│   │   ├── db.php           # Database repository
│   │   ├── auth.php         # Authentication middleware
│   │   └── validators.php   # Input validation
│   └── models/              # Data models
│       ├── User.php        # User model
│       ├── Session.php     # Session model
│       └── Vote.php        # Vote model
├── sql/                    # Database
│   └── voting_system.sql  # Schema and initial data
├── views/                 # HTML views
│   ├── components/       # Reusable UI components
│   │   ├── header.html
│   │   ├── footer.html
│   │   └── nav.html
│   └── pages/           # Main pages
│       ├── candidates.html
│       ├── dashboard.html
│       ├── login.html
│       ├── register.html
│       ├── results.html
│       ├── sessions.html
│       └── vote.html
└── README.md           # Project documentation
```

## Installation

1. Clone the repository to your XAMPP htdocs folder
2. Import `sql/voting_system.sql` into MySQL
3. Configure database connection in `php/includes/db.php`
4. Access the system through your web browser at `http://localhost/Voting System`

## Default Credentials

- Admin:
  - Username: admin
  - Password: admin123

- Test Student:
  - Username: student
  - Password: student123

## Usage Flow

### Admin
1. Login with admin credentials
2. Create new voting session
3. Add candidates to the session
4. Assign students to the session
5. Close session when voting period ends
6. View results

### Student
1. Login with student credentials
2. View assigned active sessions
3. Cast votes in active sessions
4. View results of closed sessions

## Security Considerations

1. Password Hashing
   - All passwords are hashed before storage
   - Secure password validation

2. Session Management
   - Secure PHP session handling
   - Session timeout implementation

3. Access Control
   - Role-based access verification
   - API endpoint protection

4. Data Validation
   - Input sanitization
   - Output escaping
   - Prepared SQL statements

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License
- Student: Vote in assigned sessions, view results
- Responsive, modern UI (Bootstrap 5)
- Modular, maintainable codebase

---

## Folder Structure
```
VotingSystem/
  ├── assets/
  │   ├── css/
  │   │   └── style.css
  │   └── js/   (empty or for future use)
  ├── php/
  │   ├── api/         # All backend endpoints (modular, secure)
  │   │   ├── login.php
  │   │   ├── logout.php
  │   │   ├── register.php
  │   │   ├── user_info.php
  │   │   ├── sessions.php
  │   │   ├── session_assign.php
  │   │   ├── candidates.php
  │   │   └── vote.php
  │   └── includes/    # DB connection, helpers
  │       └── db.php
  ├── sql/
  │   └── voting_system.sql
  ├── login.html
  ├── register.html
  ├── dashboard.html
  ├── sessions.html
  ├── candidates.html
  ├── vote.html
  ├── results.html
  └── README.md
```

---

## Architecture

```mermaid
graph TD;
  subgraph Frontend
    A[login.html]
    B[register.html]
    C[dashboard.html]
    D[sessions.html]
    E[candidates.html]
    F[vote.html]
    G[results.html]
  end
  subgraph API (PHP)
    H[api/login.php]
    I[api/logout.php]
    J[api/register.php]
    K[api/user_info.php]
    L[api/sessions.php]
    M[api/session_assign.php]
    N[api/candidates.php]
    O[api/vote.php]
  end
  subgraph DB
    P[(MySQL)]
  end
  A--AJAX-->H
  B--AJAX-->J
  C--AJAX-->K
  D--AJAX-->L
  D--AJAX-->M
  E--AJAX-->N
  F--AJAX-->O
  G--AJAX-->L
  G--AJAX-->N
  H--DB-->P
  I--DB-->P
  J--DB-->P
  K--DB-->P
  L--DB-->P
  M--DB-->P
  N--DB-->P
  O--DB-->P
```

---

## How to Extend
- Add new API endpoints in `php/api/` (use prepared statements, return JSON)
- Add new pages in root, use AJAX to interact with API
- Add shared helpers in `php/includes/`
- Use Bootstrap 5 for consistent UI

---

## Setup
1. Import `sql/voting_system.sql` into MySQL
2. Configure DB credentials in `php/includes/db.php`
3. Serve project via XAMPP/LAMP or similar
4. Register an admin, then manage sessions, candidates, and voting!

---

## License
MIT 