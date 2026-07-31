# MiniSocial

A full-stack social platform built with PHP — featuring a traditional session-based frontend and a complete parallel REST API, both backed by the same MySQL database. Built as a hands-on deep dive into backend fundamentals: authentication, authorization, file handling, and API design, without a framework.

---

## Features

**Authentication**
- Registration with email verification (token-based, expiring)
- Login with JWT issuance (API) or PHP sessions (web)
- Forgot / reset password flow with silent account lookup
- Resend verification email
- Logout with server-side JWT blacklisting

**User Management**
- Edit profile (name, phone, bio, location, date of birth)
- Change password (current-password verification required)
- Delete own account
- Admin panel — view all users, promote/demote roles, delete any account

**Social**
- Create posts with optional image upload (MIME-validated, 5MB limit)
- Delete own posts, or any post as admin
- Like / unlike posts — optimistic UI, no page reload
- Comment on posts — expandable threads, delete own comments or any as admin

**Security**
- Prepared statements throughout — no SQL injection surface
- Password hashing via `password_hash()` / `password_verify()`
- Server-side ownership checks on every delete/edit action
- JWT with expiry + blacklist-on-logout
- All secrets in `.env`, never committed

---

## Architecture

The app is built in two parallel layers that share the same database:

| Layer | How it works | Use case |
|---|---|---|
| **Session layer** | Traditional server-rendered PHP pages, PHP sessions for auth | The actual web app — dashboard, profile, admin panel |
| **API layer** | Stateless REST endpoints under `/api`, JWT bearer auth | Built for any external client — mobile app, SPA, Postman |

Both layers enforce the same business rules independently. Nothing in the API depends on the session layer or vice versa.

---

## Tech Stack

- **Backend:** PHP (no framework), PDO for all database access
- **Database:** MySQL / MariaDB
- **Auth:** PHP Sessions (web) + custom JWT implementation (API)
- **Email:** PHPMailer + Gmail SMTP
- **Frontend:** Vanilla HTML/CSS/JS, Lucide icons, dark mode via CSS variables
- **Testing:** Postman (manual, full endpoint coverage)

---

## Getting Started

### Prerequisites
- PHP 8+
- MySQL or MariaDB
- Apache (with `mod_rewrite` enabled) or equivalent
- A Gmail account with an [App Password](https://myaccount.google.com/apppasswords) for email sending

### Setup

1. **Clone the repo**
   ```bash
   git clone <your-repo-url>
   cd myApp
   ```

2. **Create the database**

   Import the schema (see [Database Schema](#database-schema) below for all `CREATE TABLE` statements), or run them one by one via phpMyAdmin.

3. **Configure environment variables**

   Create a `.env` file in the project root:
   ```env
   JWT_SECRET=your_random_secret_here
   DB_HOST=localhost
   DB_NAME=your_db_name
   DB_USER=your_db_user
   DB_PASS=your_db_password
   SMTP_HOST=smtp.gmail.com
   SMTP_USERNAME=your_email@gmail.com
   SMTP_PASSWORD=your_gmail_app_password
   APP_URL=http://localhost/myApp
   ```

4. **Create the uploads folder**
   ```bash
   mkdir -p uploads/posts
   chown -R www-data:www-data uploads/
   ```

5. **Serve the app**

   Point your Apache vhost (or `php -S`) at the project root and visit `index.php` to register your first account.

---

## Database Schema

| Table | Purpose |
|---|---|
| `users` | Accounts — credentials, role, verification & reset tokens |
| `posts` | User posts — text and/or image |
| `likes` | Post likes — unique per (user, post) |
| `comments` | Post comments |
| `token_blacklist` | Invalidated JWTs (post-logout) |

All foreign keys cascade on delete — removing a user or post cleans up everything related to it automatically.

---

## API Reference

Base URL: `/api`
Auth: `Authorization: Bearer <token>` header, obtained from `/api/login.php`

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/register.php` | — | Register + send verification email |
| POST | `/login.php` | — | Login, returns JWT |
| POST | `/logout.php` | JWT | Blacklist current token |
| POST | `/verify-email.php` | — | Verify account via token |
| POST | `/resend-verification.php` | — | Resend verification email |
| POST | `/forgot-password.php` | — | Send password reset email |
| POST | `/reset-password.php` | — | Reset password via token |
| PUT | `/change-password.php` | JWT | Change password |
| GET / PUT / DELETE | `/user.php?id=X` | JWT | Get, update, or delete a user |
| GET | `/users.php` | Admin | List all users |
| PUT | `/admin-roles.php` | Admin | Toggle a user's role |
| GET | `/posts.php` | — | List all posts (paginated) |
| GET / POST / DELETE | `/post.php` | GET public, rest JWT | Get, create, or delete a post |
| GET / POST | `/like.php?post_id=X` | JWT | Like status / toggle like |
| GET | `/comments.php?post_id=X` | — | List comments on a post |
| POST / DELETE | `/comment.php` | JWT | Create or delete a comment |

Full request/response examples are in the Postman collection (`/docs/postman_collection.json` if exported).

---

## Project Structure

```
myApp/
├── index.php, register.php, dashboard.php,     # Session-layer pages
│   profile.php, admin-dashboard.php...
├── *back.php                                    # Session-layer form handlers
├── config.php, load-env.php, email_config.php   # Shared configuration
├── api/                                         # REST API layer
│   ├── config.php, auth.php                     # API bootstrap + JWT logic
│   └── *.php                                    # One file per resource
├── uploads/posts/                               # User-uploaded images
└── .env                                          # Secrets (not committed)
```

---

## Development Log

This project was built incrementally over 20 documented sessions, each covering one feature or fix in depth — from the first session-based login to the final security audit. The full day-by-day journal, including design decisions, bugs encountered, and lessons learned, is available in [`JOURNAL.md`](./JOURNAL.md).

---

## License

Personal learning project — free to reference or fork.