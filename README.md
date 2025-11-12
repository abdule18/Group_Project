# Remote Tech Support Service (PHP + MySQL)

A minimal, functional ticketing app where users can register, log in, open support tickets, and collaborate with admins via comments. Built for CIS 344 Group Project requirements (PHP, HTML/CSS/JS, MySQL).

## Roles
- **Database**: Moubarak Congacou
- **Frontend**: Rokia Touray
- **Backend**: Abdule Touray

## Features
- User registration & login (password hashing, sessions)
- Create tickets with category & priority
- View own tickets (users) and all tickets (admins)
- Ticket detail view with comments
- Admin controls: update status, assign tickets
- CSRF protection, prepared statements, input sanitization
- SQL `JOIN`s and a `VIEW` (`v_tickets_full`) for graded review

## Tech
- PHP 8+, MySQL 8+, Apache/Nginx
- Frontend: HTML/CSS/JS (vanilla)
- Backend: PHP (PDO)
- DB: MySQL with foreign keys and joins

## Setup

1. **Clone** or download and extract this project into your web root (e.g., `htdocs/remote-tech-support`).  
2. Copy `config/config.sample.php` to `config/config.php` and set your DB credentials.
3. Create a database (e.g., `remote_support`), then run the SQL:
   ```sql
   -- In order
   SOURCE sql/schema.sql;
   SOURCE sql/sample_data.sql;
   ```
4. Ensure your web server serves the `public/` directory as the document root for this app (or map `/` to this folder).  
5. Visit `http://localhost/` (or your host). Register a user or login as admin:
   - **Admin**: `admin@example.com` / `Admin123!`

> If `public/` is not your document root, configure your server or move files as needed. For quick tests with PHP's built-in server:
```bash
php -S localhost:8080 -t public
```

## Security Notes
- Uses prepared statements (PDO) to mitigate SQL injection.
- Basic CSRF protection for POST actions.
- Passwords are hashed with `password_hash()` (bcrypt).

## Database Design
**Tables**: `users`, `categories`, `priorities`, `statuses`, `tickets`, `ticket_comments`  
**Relationships**:
- `tickets.user_id -> users.id` (reporter)
- `tickets.assigned_to -> users.id` (assignee, nullable)
- `tickets.category_id -> categories.id`
- `tickets.priority_id -> priorities.id`
- `tickets.status_id -> statuses.id`
- `ticket_comments.ticket_id -> tickets.id`
- `ticket_comments.user_id -> users.id`

**Joins Example (used in app)**:
```sql
SELECT t.id, t.title, s.name AS status, p.name AS priority, c.name AS category
FROM tickets t
JOIN statuses s ON s.id = t.status_id
JOIN priorities p ON p.id = t.priority_id
JOIN categories c ON c.id = t.category_id
WHERE t.user_id = ?;
```

**View**: `v_tickets_full` aggregates reporter/assignee, status, priority, and category via joins.

## Folder Structure
```
remote-tech-support/
├── config/
│   ├── config.php
│   ├── csrf.php
│   └── db.php
├── lib/
│   ├── auth.php
│   ├── helpers.php
│   └── validation.php
├── public/
│   ├── assets/
│   │   ├── app.js
│   │   └── styles.css
│   ├── dashboard.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── register.php
│   ├── ticket.php
│   └── tickets.php
├── sql/
│   ├── schema.sql
│   └── sample_data.sql
└── README.md
```

## User Guide (Quick)
1. Register or login.
2. Submit a ticket from Dashboard.
3. Open your ticket to add comments.
4. Admins open **All Tickets** to assign and change status.

## Credits
Built by Moubarak Congacou (DB), Rokia Touray (Frontend), Abdule Touray (Backend).
