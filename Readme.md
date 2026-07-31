# 🔐 PHP Authentication System — Day 1

This repository documents my step-by-step backend journey, starting with the fundamentals of authentication using PHP sessions.

This README covers Day 1 only, strictly based on what is currently implemented and shown in the screenshots.

## 📅 Day 1 — Session-Based Login System
### 🎯 Goal

Understand how authentication works on the backend using PHP:

Sessions

Login flow

Protected pages

Logout handling

## ✅ What Was Implemented (Day 1)
### 1️⃣ Login Page (index.php)

A login form with:

Email field

Password field

Clean, centered UI with a gradient background

PHP session started at the top of the file

Displays feedback messages using session variables

### 2️⃣ Session Handling

Sessions are initialized using:

session_start();


User email is stored in $_SESSION after successful login

Session data is used to control access to pages

### 3️⃣ Authentication Logic

User credentials are checked on form submission

If credentials are valid:

User is redirected to the dashboard

If credentials are invalid:

An error message is stored in the session

### 4️⃣ Protected Dashboard (dashboard.php)

Dashboard page is only accessible after login

Displays a personalized welcome message:

Welcome, user@email.com


Contains a Logout button

### 5️⃣ Logout System (logout.php)

Logout destroys the session using:

session_destroy();


User is redirected back to the login page

Prevents access to dashboard after logout

### 6️⃣ Database Configuration (config.php)

Database connection established using PDO

Credentials stored in a separate configuration file

Errors handled with try...catch

## 🗂️ Project Structure (Day 1)
/project-root
│
├── config.php        # PDO database connection
├── index.php         # Login page
├── dashboard.php     # Protected page (session-based)
├── logout.php        # Session destruction
└── README.md

## 🖼️ Screenshots (Day 1)
Login Page
![Login Page Screenshot](./screenshots1/Screenshot_2025-12-22_17_47_30.png)

Dashboard After Login
![Dashboard Page Screenshot](./screenshots1/Screenshot_2025-12-22_17_47_41.png)

Codespace (PHP + Sessions)
![Codespace Screenshot](./screenshots1/Screenshot_2025-12-22_18_36_18.png)

📌 Screenshots reflect the exact state of the project at Day 1.

## 🛠️ Tech Used (Day 1 Only)

PHP

PHP Sessions

MySQL (PDO)

HTML & CSS

Apache (Localhost)

## 📌 Key Takeaways (Day 1)

Authentication starts with session control

Backend logic decides access — not the UI

Sessions persist state across requests

Logout is just as important as login

## 🔜 What Comes Next (Not Implemented Yet)

These are not part of Day 1:

Password hashing

Database-driven users table

Registration system

Security hardening

Middleware / MVC

They will be introduced incrementally in future days.

👤 Author

# 🔐 PHP Authentication System — Day 2
## 📅 Day 2 — User Registration & Password Security
## 🎯 Focus

Build a proper user registration flow and introduce basic security practices for handling user credentials.

## ✅ What Was Implemented (Day 2)

### 👉 A dedicated registration page (register.php) with:

Email input

Password and confirm-password fields

### 👉 Server-side validation to ensure:

Valid email format

Matching passwords

Minimum password length

### 👉 Prevention of duplicate accounts by checking if an email already exists

### 👉 Secure password storage using:

password_hash()


### 👉 Database operations handled with PDO prepared statements

### 👉 Session-based error and success messages for user feedback

### 👉 Clear redirect flow between registration and login pages

## 🖼️ Screenshots (Day 2)
Codespace 
![Login Page Screenshot](./screenshots1/Screenshot_2025-12-26_08_38_41.png)

Different output based on conditions
![Dashboard Page Screenshot](./screenshots1/Screenshot_2025-12-26_08-04-09.png)
![Dashboard Page Screenshot](./screenshots1/Screenshot_2025-12-26_08-06-20.png)
![Dashboard Page Screenshot](./screenshots1/Screenshot_2025-12-26_08-07-19.png)
![Dashboard Page Screenshot](./screenshots1/Screenshot_2025-12-26_08-09-17.png)
![Dashboard Page Screenshot](./screenshots1/Screenshot_2025-12-26_08-28-09.png)

## 🧠 Key Takeaways (Day 2)

This stage introduced real backend responsibilities:

Validating user input on the server

Protecting user credentials

Enforcing data integrity before database writes

Managing user feedback using sessions

### Registration is more than a form — it’s the first layer of application security.

# 🔐 PHP Authentication System — Day 3
## 📅 Day 2 — Email Verification System
## 🎯 Goal

Implement secure email verification to ensure only valid users can activate their accounts after registration.

## 🛠️ What was implemented

👉Token-based email verification

Verification token is received via URL (verify.php?token=...)

Token is securely checked against the database using prepared statements (PDO)

👉Validation checks

-Invalid or missing token handling

-Already verified account detection

-Token expiration check using timestamps

👉Account activation

-Marks user as verified (is_verified = 1)

-Clears verification token and expiration fields after success

👉Session feedback

-Success message stored in session for login flow

👉User feedback UI

-Dynamic success, error, and info states

-Clean verification page with visual indicators and clear messages

## 🔐 Key concepts learned

-Secure token validation

-Time-based token expiration

-Email verification logic

-Defensive backend programming

-Separation of authentication states (registered vs verified)

## 📂 Files involved

-verify.php

-Database users table (verification token & expiry fields)

## 🖼️ Screenshots (Day 3)
Codespace 
![Login Page Screenshot](./screenshots1/Screenshot_2025-12-28_19_13_34.png)

Different output based on conditions
![Dashboard Page Screenshot](./screenshots1/Screenshot_2025-12-28_18-52-56.png)
![Dashboard Page Screenshot](./screenshots1/Screenshot_2025-12-28_18-54-48.png)
![Dashboard Page Screenshot](./screenshots1/Screenshot_2025-12-28_19-09-15.png)
![Successful](./screenshots1/Screenshot_2025-12-28_19-09-34.png)


# Day 4 – Real Email Verification with PHPMailer
## 🎯 Goal

On Day 4, I implemented real email delivery for MiniSocialApp using PHPMailer and Gmail SMTP.

## 🛠️ The application can now:

-Send verification emails to newly registered users

-Resend verification emails on user request

-Enforce email verification before allowing login

This was achieved by configuring a dedicated email_config.php file with PHPMailer, a Gmail App Password, and secure SMTP settings. With this update, the authentication flow now mirrors real-world production systems where email ownership must be confirmed before access is granted.

This milestone marks a major step from a local demo system to a production-ready authentication workflow.

## 🖼️ Screenshots (Day 4)
Codespace 
![Codespace](./screenshots1/Screenshot_2026-01-08_16_37_08.png)

### 👉 For newly registering user
![Dashboard Page Screenshot](./screenshots1/Screenshot_2026-01-08_16-21-41.png)
![email](./screenshots1/Screenshot_2026-01-08_16-22-37.png)

### 👉 For registered but unverified user requesting for email resend
![Dashboard Page Screenshot](./screenshots1/Screenshot_2026-01-08_16-10-51.png)
![email](./screenshots1/Screenshot_2026-01-08_16-15-21.png)

# 📅 Day 5 – Secure Password Reset System (Forgot & Reset Password)

On Day 5, I implemented a complete and secure password recovery workflow for the MiniSocialApp, similar to what is used in real-world platforms like GitHub and LinkedIn.
This feature allows users to safely regain access to their accounts while protecting the system from abuse and information leaks.

## 🔐 Why “Forgot Password” Matters

-Password loss is unavoidable. A secure recovery system must:
-Help legitimate users recover access
-Prevent attackers from exploiting reset flows
-Avoid revealing whether an email exists

This implementation prioritizes security without sacrificing usability.

## 🧩 Feature Overview
### 1️⃣ Forgot Password Request (forgot_passwd.php)

-Users submit their email to request a reset.
Key logic:
-Email format validation
-Silent user lookup (no account disclosure)
-Secure reset token generation
-1-hour token expiration
-Reset link sent via real email (PHPMailer + Gmail SMTP)
-Generic success message for all cases

🔒 Prevents email enumeration attacks.

### 2️⃣ Email-Based Reset Link

Users receive a one-time, time-limited reset link via email.
If they didn’t request it, they can safely ignore it.

### 3️⃣ Token Validation (reset_passwd.php)

On link access:
-Token existence is verified
-Expiration is checked
-Invalid or expired tokens are rejected immediately
-This blocks replay attacks and old-link abuse.

### 4️⃣ Password Reset Rules

Before updating:
-Passwords must match
-Minimum length enforced
-New password cannot equal the old one

This prevents fake resets and encourages better security practices.

### 5️⃣ Secure Password Update

-Password hashed with password_hash()
-Reset token invalidated immediately
-Expiration cleared
-User redirected to login
-Tokens become unusable after a successful reset.

🛡️ Security Practices Applied

-Token-based recovery
-Time-limited reset links
-Strong password hashing
-No account existence disclosure
-One-time token usage
-Old-password reuse prevention

## 🖼️ Screenshots (Day 5)
Codespace 
![Login Page Screenshot](./screenshots1/Screenshot_2026-01-12_16_59_54.png)

Typical Process When Resetting Password
![Dashboard Page Screenshot](./screenshots1/Screenshot_2026-01-12_16-22-45.png)
![Dashboard Page Screenshot](./screenshots1/Screenshot_2026-01-12_16-23-18.png)
![Dashboard Page Screenshot](./screenshots1/Screenshot_2026-01-12_16-24-23.png)
![Successful](./screenshots1/Screenshot_2026-01-12_16-25-16.png)


# Day 6 – User Profile Management & Account Control

On Day 6, I implemented a full user profile system that allows authenticated users to manage their account securely and independently.

## What Was Added

### 1. Profile Page (profile.php)

-Restricted access to logged-in users only (session-based authentication).

-Fetches and displays user data directly from the database.

-Clean UI showing:

-Avatar placeholder

-Email

-Account creation date

-Last profile update date

### 2. Profile Actions Backend (profileback.php)
Handled multiple sensitive user actions using a single controller pattern:

-Update Personal Information

--Full name, phone, bio, location, date of birth

--Uses prepared statements to prevent SQL injection

-Change Password

--Verifies current password before update

--Enforces minimum password length

--Uses password hashing for security

-Delete Account

--Permanent account deletion with confirmation

--Session destroyed immediately after deletion

### 3. Profile Styling (profile-style.css)

-Responsive, modern layout using CSS Grid

-Clear separation between profile info, forms, and danger zone

-Mobile-friendly adjustments for smaller screens

## Key Concepts Practiced

-Secure session handling

-Authorization checks

-Form handling with multiple actions

-Password verification & hashing

-Account lifecycle management (update → secure → delete)

-Clean separation of logic, UI, and styles

## 🖼️ Screenshots (Day 6)
Codespace 
![Workspace](./screenshots1/Screenshot_2026-01-20_07-12-01.png)

Personal info section
![Personal info sectfion](./screenshots1/Screenshot_2026-01-20_06-53-44.png)

Password section
![Password section](./screenshots1/Screenshot_2026-01-20_06-54-05.png)

Delete section
![Delete section](./screenshots1/Screenshot_2026-01-20_06-54-22.png)

Full view
![Full view](./screenshots1/Screenshot_2026-01-20_06-57-17.png)

# Day 7 – Selective Dark Mode Integration (Dashboard & Profile)

On Day 7, I introduced a selective Dark Mode system to improve user experience while keeping the app clean and intentional.

## 🎯 Design Decision

Dark mode was added only to authenticated pages:

✅ Dashboard

✅ Profile

The login page intentionally remains light-only to:

-Avoid over-design

-Keep the entry point simple and fast

-Maintain focus on authentication (short, public-facing view)

## 🌗 What Was Implemented

✅ Theme toggle button (Light 🌞 / Dark 🌙) on Dashboard and Profile

✅ CSS variables (:root + [data-theme="dark"]) for scalable theming

✅ Persistent theme state using localStorage

✅ Smooth transitions between themes

✅ Shared theme behavior across Dashboard and Profile

## 🛠 Modified Files

✅ dashboard.php

--Inline CSS with light/dark variables

--Theme toggle logic with localStorage

✅ profile.php

--Theme toggle button + JS logic

--Syncs with Dashboard theme

✅ profile-style.css

--Refactored to use CSS variables

--Added dark-mode color palette

## 🖼️ Screenshots (Day 7)
Unchanged login page 
![login](./screenshots1/Screenshot_2026-01-26_21-22-33.png)

Dark dashboard page
![Dark dashboard page](./screenshots1/Screenshot_2026-01-26_21-23-09.png)

Light dashboard page
![Light dashboard page](./screenshots1/Screenshot_2026-01-26_21-23-27.png)

Dark profile page
![Dark profile](./screenshots1/Screenshot_2026-01-26_21-24-17.png)

Light profile page
![Light profile](./screenshots1/Screenshot_2026-01-26_21-24-33.png)

## ✅ Outcome

-Cleaner UX with context-aware theming

-No visual overload on the login page

-Dark mode feels intentional, not cosmetic

-Strong separation between public and authenticated UI

🧩 This step reinforces a core UI principle: not every page needs every feature.

#  Day 8 – Admin System & Role-Based Access Control
## Overview

On Day 8, an Administrator system was introduced to MiniSocialApp. This adds role-based access control, a secure admin dashboard, and protections against unauthorized access to admin-only pages.

## Key Features Implemented
### 👥 User Roles

-Added a role column to the users table (user / admin)

-User role is loaded into the session at login

-Session now stores:

--id_user

--user_email

--full_name

--user_role

### 🔐 Role-Based UI Rendering

Dashboard now conditionally displays the “Admin Panel” button

Only users with role = admin can see and access admin features

### 🛡️ Admin Access Protection

-Created a reusable admin-check.php

-Required in all admin pages

-Prevents:

--Non-logged-in users

--Logged-in but non-admin users

-Redirects unauthorized users back to the dashboard with an error message

### ⚙️ Admin Dashboard

-Centralized admin interface with:

--User statistics (total, verified, unverified, admins)

--Full users table

-Admin actions:

--Delete users (cannot delete self)

--Promote/Demote users (admin ↔ user)

--All actions handled securely via POST requests

### 🎨 Styling & UX

-Dedicated admin dashboard styles

-Consistent layout with the rest of the app

-Integrated Dark Mode support

-Clear badges for:

--User roles

--Verification status

## 🖼️ Screenshots (Day 8)
 
![login](./screenshots1/Screenshot_2026-02-04_18-18-12.png)

 
![Dark dashboard page](./screenshots1/Screenshot_2026-02-04_18-18-54.png)

 
![Light dashboard page](./screenshots1/Screenshot_2026-02-04_18-19-30.png)

 
![Dark profile](./screenshots1/Screenshot_2026-02-04_18-20-10.png)

 
![Light profile](./screenshots1/Screenshot_2026-02-04_18-21-01.png)

![login](./screenshots1/Screenshot_2026-02-04_18_58_14.png)


## Why This Matters

This milestone transforms MiniSocialApp from a basic authentication app into a multi-role system, laying the foundation for:

Moderation tools

Scalable permissions

Real-world application architecture

#  Day 9 – API Foundation & JWT Authentication

On Day 9, I started expanding MiniSocialApp beyond server-rendered pages by introducing a dedicated REST API layer.
A new /api directory was created to host endpoints for authentication and user management, preparing the project for future integrations with mobile apps, SPAs, or other services.

## Key Implementations
### API Structure

Created a new /api directory to organize backend endpoints. Planned APIs include:

login
registration
user
users
change-password

This separates API logic from the traditional PHP pages, making the architecture more scalable.

### JWT Authentication System

Implemented a custom JWT (JSON Web Token) authentication mechanism.

Features include:

Token generation with HS256 signing

Payload containing:

user_id

email

role

iat (issued at)

exp (expiration – 24h)

Token verification with signature validation

Token extraction from Authorization Bearer headers

Utility middleware functions:

requireAuth() – protects endpoints requiring authentication

requireAdmin() – restricts admin-only API routes

### API Configuration Layer

Created /api/config.php to standardize API behavior:

Loads environment variables

Reuses the parent database configuration

Handles CORS headers

Processes OPTIONS preflight requests

Provides helper functions:

sendResponse()

getRequestBody()

### Environment Management

Improved security and configuration handling by introducing:

.env file for environment variables

Custom environment loader

Generated and stored JWT_SECRET securely

This ensures sensitive data like secrets and credentials stay outside the codebase.

### Server Configuration

Added .htaccess to support API routing and improve request handling.

##  Screenshots (Day 9)
 
![login](./screenshots1/Screenshot_2026-03-12_16_48_04.png)

# Day 10 – API Authentication Endpoints (Register & Login)

## Overview

On Day 10, I moved from building the API foundation to implementing and testing the first working authentication endpoints:
➡️ /api/register
➡️ /api/login

Both endpoints were fully tested using Postman, with well-structured request bodies and clean JSON responses.

## Register Endpoint (/api/register)

Handles new user creation with proper validation and security.

Key Features:
    Validates email format and password length
    Prevents duplicate registrations (409 Conflict)
    Hashes passwords using password_hash()
    Generates email verification token with expiration
    Stores user with default role (user) and unverified status
Response:
    201 Created on success
Returns:
    success message
    user_id
    verification_required flag

## Login Endpoint (/api/login)

Authenticates users and issues a JWT token.

Key Features:
    Validates request method and input fields
    Verifies credentials using password_verify()
    Blocks login if email is not verified (403)
    Generates JWT token with user data (id, email, role)
Response:
    200 OK on success
Returns:
    success message
    JWT token
    authenticated user details

## API Testing (Postman)
Structured raw JSON requests
Verified status codes:
    400 → Bad request
    401 → Invalid credentials
    403 → Not verified
    409 → Conflict
    201 / 200 → Success
Confirmed consistent and predictable API responses

## Key Takeaways
Transitioned from theory (Day 9) to real working API endpoints
Understood proper use of HTTP status codes
Built secure authentication flow with validation + hashing + JWT
Practiced real-world API testing workflows

##  Screenshots (Day 10)

### /api/register 
![register](./screenshots1/Screenshot_2026-03-23_13_06_20.png)
### /api/login
![login](./screenshots1/Screenshot_2026-03-23_13_16_07.png)

# Day 11 – User Management APIs & Role-Based Access Control

## Overview

On Day 11, I expanded the MiniSocialApp API by implementing a full set of user management endpoints, with strong emphasis on authorization, security, and proper access control.

This stage moves the API closer to a real-world backend system, where different users have different permissions and capabilities.

## Role-Based Access Control (RBAC)

All endpoints are protected using JWT authentication and role checks:

Admin-only routes for sensitive operations
User-restricted actions to ensure users can only act on their own data
Clear separation between admin privileges and regular user capabilities

## Implemented Endpoints
### GET /api/users (Admin Only)

Retrieve all users in the system.

Features:

Protected with requireAdmin()
Returns total user count and user list
Ordered by latest registrations

### GET /api/user?id={id}

Retrieve a single user’s data.

Access Control:

Users can fetch their own profile only
Admins can fetch any user

Response:

Full user profile including metadata (email, bio, role, timestamps)

### PUT /api/user?id={id}

Update user profile information.

Features:

Users can update their own data only
Admins can update any user
Supports fields like name, phone, bio, and location
Updates updated_at timestamp automatically

### DELETE /api/user?id={id} (Admin Only)

Delete a user from the system.

Features:

Restricted to admins via requireAdmin()
Prevents unauthorized deletions
Clean success/error responses

### PUT /api/change-password

Allow authenticated users to securely change their password.

Security Measures:

Requires current password verification
Enforces password confirmation match
Validates minimum password length
Uses password_hash() for secure storage
Updates timestamp on success

## API Testing (Postman)

All endpoints were thoroughly tested using Postman:

Verified role-based restrictions (user vs admin)
Tested all HTTP methods: GET, PUT, DELETE
Validated responses for:
200 → Success
400 → Bad request
401 → Unauthorized
403 → Forbidden
404 → Not found
500 → Server errors

##  Screenshots (Day 11)

### GET api/users (admin GETting all users)
![register](./screenshots1/Screenshot_2026-03-28_19_54_31.png)

### GET api/user (GETing by user before modifying user profile)
![login](./screenshots1/Screenshot_2026-03-28_20_17_31.png)

### PUT api/user (user modifying his profile)
![login](./screenshots1/Screenshot_2026-03-28_21_50_46.png)

### GET api/user (GETing by user after modifying user profile)
![login](./screenshots1/Screenshot_2026-03-28_21_59_31.png)

### GET api/change-password (user changing his password)
![login](./screenshots1/Screenshot_2026-03-28_22_34_01.png)

### DELETE api/user (user trying to delete himself)
![login](./screenshots1/Screenshot_2026-03-28_22_09_45.png)

### DELETE api/user (admin successfully deleting user)
![login](./screenshots1/Screenshot_2026-03-28_22_13_20.png)

### GET api/user (trying to GET deleted user)
![login](./screenshots1/Screenshot_2026-03-28_22_14_51.png)

# Day 12 – Email Verification & Password Reset APIs

## Overview

On Day 12, I completed the remaining authentication flow endpoints, mirroring what the session layer already had in verify.php, resend-verification.php, forgot_passwd.php and reset_passwd.php — but now exposed as a clean REST API.

➡️ /api/verify-email
➡️ /api/resend-verification
➡️ /api/forgot-password
➡️ /api/reset-password

## Endpoints

### /api/verify-email
Activates a user account using the token sent during registration.

Key Features:
    Accepts token via POST body
    Validates token existence, expiry, and already-verified state
    Clears token fields after successful verification
Response:
    200 → Email verified successfully
    400 → Invalid token
    409 → Already verified
    410 → Token expired

### /api/resend-verification
Issues a fresh verification token and resends the email.

Key Features:
    Rejects already-verified accounts (409)
    Generates new token with 24h expiry
    Sends email via PHPMailer + Gmail SMTP
    Returns 200 even if email not found (prevents account enumeration)
Response:
    200 → Email sent (or silently skipped if not found)
    409 → Already verified

### /api/forgot-password
Initiates password recovery by emailing a one-time reset link.

Key Features:
    Silent user lookup — never reveals whether email is registered
    Generates reset token with 1h expiry
    Sends reset link via PHPMailer
    Uses APP_URL from .env for the reset link base
Response:
    200 → Generic success in all cases (security)
    400 → Invalid email format

### /api/reset-password
Completes the password reset using the token from the email.

Key Features:
    Validates token existence and expiry
    Enforces password confirmation match and minimum length
    Prevents reuse of the current password
    Clears reset token after successful update
Response:
    200 → Password reset successfully
    400 → Validation errors / same password reuse
    410 → Token expired

## API Testing (Postman)
Tested full flow end to end:
    Registered user → received verification email → verified via token
    Attempted login before verification → blocked (403)
    Requested password reset → received email → reset with new password
    Attempted reuse of reset token → rejected (410)
Verified status codes:
    200 → Success
    400 → Bad request / validation failure
    403 → Not verified
    409 → Conflict
    410 → Expired token

## Key Takeaways
Understood why generic responses protect against account enumeration attacks
Learned the difference between token expiry (410) and invalid token (400)
Saw how the API layer mirrors session-based logic with cleaner separation of concerns
Completed the full authentication lifecycle across both layers of the app

## Screenshots (Day 12)

### /api/resend-verification
![resend-verification](./screenshots1/Screenshot_2026-04-12_18_51_24.png)
### /api/verify-email
![verify-email](./screenshots1/Screenshot_2026-04-13_08_12_35.png)
### /api/forgot-password
![forgot-password](./screenshots1/Screenshot_2026-04-13_08_18_54.png)
### /api/reset-password
![reset-password](./screenshots1/Screenshot_2026-04-13_08_53_34.png)

# Day 13 – Bug Fixes, JWT Logout & Admin Role Toggle API

## Overview

On Day 13, I focused on hardening the existing API layer — fixing a scoping bug that broke authenticated endpoints, completing the logout system with real token invalidation, and finalizing the admin role toggle endpoint.

➡️ /api/logout
➡️ /api/admin-roles

## What Was Fixed & Implemented

### 🐛 PHP Scoping Bug (auth.php)

After adding the token blacklist check to requireAuth(), all authenticated endpoints started returning 500.

Root cause:
$pdo is created in global scope but PHP functions cannot see global variables unless explicitly declared inside them

Fix:
Added global $pdo; as the first line inside requireAuth() so the blacklist query has access to the database connection

### 🔒 JWT Logout (/api/logout)

Implemented real server-side logout by blacklisting the token.

Key Features:
    Requires valid JWT (authenticated endpoint)
    Extracts raw token from Authorization header
    Inserts token into token_blacklist table with its expiry timestamp
    requireAuth() now rejects any blacklisted token on future requests
    Token becomes unusable immediately after logout even if not yet expired
Response:
    200 → Logged out successfully
    500 → Logout failed

### 👥 Admin Role Toggle (/api/admin-roles)

Allows admins to promote or demote other users.

Key Features:
    Admin-only endpoint via requireAdmin()
    Accepts user_id in request body
    Fetches current role and toggles it (user → admin, admin → user)
    Admins cannot change their own role (403)
    Returns previous and new role in response for confirmation
Response:
    200 → Role updated successfully with previous_role and new_role
    400 → user_id missing
    403 → Attempting to change own role
    404 → User not found

### 🛠️ user.php Improvements

Fixed PUT endpoint to include dob (date of birth) field which was present in the session layer but missing from the API

Fixed DELETE endpoint to allow users to delete their own account, not just admins

## API Testing (Postman)
Confirmed logout blacklists token correctly
Verified blacklisted token returns 401 on subsequent requests
Tested role toggle: user → admin on first hit, admin → user on second hit
Confirmed admins cannot toggle their own role (403)
Validated dob saves correctly via PUT /api/user

## Key Takeaways
PHP functions are isolated from global scope — always declare global $pdo inside functions that need it
JWT logout requires server-side blacklisting — discarding the token client-side alone is not enough
A toggle endpoint is cleaner than separate promote/demote endpoints when the logic is symmetric

## 🖼️ Screenshots (Day 13)

### PUT /api/admin-roles (user → admin)
![admin-roles toggle user to admin](./screenshots1/Screenshot_2026-04-19_22_17_01.png)

### PUT /api/admin-roles (admin → user)
![admin-roles toggle admin to user](./screenshots1/Screenshot_2026-04-19_22_17_07.png)

### POST /api/logout
![logout successful](./screenshots1/Screenshot_2026-04-19_22_20_32.png)

Looking at the screenshots — feed working with images and text posts, create tab, me tab all confirmed. Here's the caption:


# Day 14 – Posts & Dashboard Redesign (Session Layer)

## Overview

On Day 14, I introduced the core social feature of MiniSocialApp — posts.
This involved redesigning the dashboard from scratch and building the full
create/delete post flow with image upload support on the session layer.

➡️ dashboard.php (redesigned)
➡️ postback.php (new)
➡️ uploads/posts/ (new)
➡️ posts table (new)

## Database

### posts table
New table created to store all posts:

    id          → auto-increment primary key
    user_id     → foreign key referencing users(id), CASCADE on delete
    content     → post text body (nullable — image-only posts allowed)
    image_path  → relative path to uploaded image (nullable)
    created_at  → auto-set on insert
    updated_at  → auto-updates on row change

Foreign key constraint ensures no orphan posts exist if a user is deleted.
Indexes on user_id and created_at keep feed queries fast.

## Dashboard Redesign (dashboard.php)

Replaced the old card-based dashboard with a sticky navbar + tab layout.

### Navbar
    MiniSocial brand on the left
    Theme toggle (light/dark, persisted via localStorage)
    Admin panel shortcut (only visible to admins)
    Avatar circle linking to profile (shows first letter of name)

### Tabs
Three tabs with no page reload — pure JS switching:

    Feed      → public post feed, newest first
    New Post  → compose UI with image upload
    Me        → quick profile summary with links

### Feed Tab
    Fetches all posts via JOIN with users table to get author info
    Posts display author avatar (initial letter), name, timestamp, content, image
    Delete button visible only to post owner or admin
    Staggered fade-up animation on post cards
    Empty state shown when no posts exist

### New Post Tab
    Compose card with textarea (1000 char limit + live counter)
    Image upload button (JPG, PNG, GIF, WEBP — max 5MB)
    Shows selected filename after picking an image
    After successful post, auto-switches back to Feed tab

### Me Tab
    Shows logged-in user's avatar, full name, email
    Links to Edit Profile and Log out

## Post Handler (postback.php)

Single controller handling two actions via hidden form field.

### create_post
    Validates at least content or image is present
    Enforces 1000 character limit
    Validates image MIME type by reading file bytes (not just extension)
    Enforces 5MB size limit
    Generates unique filename with uniqid() to prevent collisions
    Moves file to uploads/posts/ via move_uploaded_file()
    Stores relative image path in DB
    Uses Post/Redirect/Get pattern to prevent form resubmission

### delete_post
    Fetches post from DB before deleting to verify ownership
    Server-side check — owner or admin only
    Deletes DB row then removes image file from server with unlink()
    Prevents orphan files accumulating in uploads/posts/

## Bug Fixed Along The Way
    Foreign key error on posts table creation — caused by INT vs INT UNSIGNED
    mismatch between users.id and posts.user_id. Fixed by matching types.
    Image upload failing with 500 — uploads/posts/ was owned by black instead
    of www-data. Fixed with: sudo chown -R www-data:www-data uploads/

## Key Takeaways
    JOIN queries let you pull related data without duplicating it across tables
    MIME type validation is safer than extension checking for file uploads
    Post/Redirect/Get prevents duplicate submissions on browser refresh
    File cleanup on delete is just as important as the delete query itself
    PHP functions don't see global variables — always declare global $pdo inside functions

## 🖼️ Screenshots (Day 14)

### Admin's feed
![admin feed](./screenshots1/Screenshot_2026-04-27_06-32-42.png)

### User's feed (success message)
![user feed with success](./screenshots1/Screenshot_2026-04-27_06-31-26.png)

### New Post tab
![new post tab](./screenshots1/Screenshot_2026-04-27_06-33-04.png)

### Me tab
![me tab](./screenshots1/Screenshot_2026-04-27_06-33-20.png)

# Day 15 – Posts API (GET, POST, DELETE)

## Overview

On Day 15, I built the API layer for posts — mirroring what the session
layer already does in postback.php but now exposed as REST endpoints.

➡️ /api/posts.php
➡️ /api/post.php

## Endpoints

### GET /api/posts.php — fetch all posts (public)

Returns all posts newest first, joined with author info.

Key Features:
    No authentication required — publicly accessible
    Pagination via query params: ?limit=20&offset=0
    Limit capped at 100 to prevent abuse
    Returns total count alongside posts for frontend pagination
Response:
    200 → { total, limit, offset, posts[] }
    405 → Method not allowed

### GET /api/post.php?id=X — fetch single post (public)

Returns a single post by ID with full author info.

Key Features:
    No authentication required
    Validates id is numeric
Response:
    200 → { post: { id, content, image_path, created_at, updated_at, user_id, author_name, author_email } }
    400 → Valid post ID required
    404 → Post not found

### POST /api/post.php — create post (JWT required)

Creates a new post with optional image upload.

Key Features:
    Requires valid JWT token
    Body must be form-data (not raw JSON) to support file upload
    Validates at least content or image is present
    Enforces 1000 character content limit
    Validates image MIME type by reading file bytes
    Enforces 5MB image size limit
    Generates unique filename with uniqid() to prevent collisions
    Stores relative image path in DB
Response:
    201 → Post created successfully + post_id
    400 → Validation errors
    401 → Unauthorized
    500 → Upload or insert failure

### DELETE /api/post.php?id=X — delete post (JWT required)

Deletes a post by ID. Owner or admin only.

Key Features:
    Fetches post before deleting to verify ownership
    Owner can delete their own posts
    Admin can delete any post
    Deletes image file from server with unlink() after DB row removal
Response:
    200 → Post deleted successfully
    400 → Valid post ID required
    403 → No permission
    404 → Post not found
    500 → Delete failure

## API Testing (Postman)
Tested full flow:
    GET /api/posts.php → all posts returned with total, limit, offset metadata
    GET /api/posts.php?limit=3&offset=1 → pagination working correctly
    GET /api/post.php?id=5 → single post with author info returned
    POST /api/post.php → created post with image using form-data + user JWT (201)
    DELETE /api/post.php?id=4 → deleted post using admin JWT (200)
Verified authorization rules:
    POST without token → 401
    DELETE on post you don't own (non-admin) → 403
    DELETE on non-existent post → 404

## Key Takeaways
POST /api/post.php must use form-data not raw JSON — PHP's $_FILES only
works with multipart/form-data. Sending JSON loses the file entirely.
Pagination metadata (total, limit, offset) should always be returned
alongside the data so any client can calculate pages without extra requests.
Image cleanup on delete applies to the API layer too — unlink() must
reference the correct path relative to the API file's location using __DIR__

## 🖼️ Screenshots (Day 15)

### GET /api/posts.php — all posts
![GET all posts](./screenshots1/Screenshot_2026-05-01_11_39_02.png)

### GET /api/posts.php?limit=3&offset=1 — pagination
![GET posts with pagination](./screenshots1/Screenshot_2026-05-01_11_45_41.png)

### GET /api/post.php?id=5 — single post
![GET single post](./screenshots1/Screenshot_2026-05-02_09_03_05.png)

### POST /api/post.php — create post with image (user token)
![POST create post](./screenshots1/Screenshot_2026-05-02_09_27_52.png)

### DELETE /api/post.php?id=4 — delete post (admin token)
![DELETE post](./screenshots1/Screenshot_2026-05-02_09_31_31.png)

# Day 16 – Likes (Session + API + Optimistic UI)

## Overview

On Day 16, I implemented the full likes feature across both layers
simultaneously — session layer, API layer, and a no-refresh optimistic
UI update so liking feels instant like a real social app.

➡️ likeback.php (new)
➡️ dashboard.php (updated)
➡️ /api/like.php (new)

## Database

### likes table
    id         → auto-increment primary key
    user_id    → FK referencing users(id), CASCADE on delete
    post_id    → FK referencing posts(id), CASCADE on delete
    created_at → auto-set on insert
    UNIQUE KEY (user_id, post_id) → makes double-liking structurally
    impossible at the DB level, not just at the PHP level

The UNIQUE KEY is what enables INSERT IGNORE toggle logic and guarantees
data integrity even if there's a bug in application code.

## Session Layer

### likeback.php
Single toggle handler — no separate like/unlike endpoints needed.

Key Logic:
    Session guard + POST-only check
    Casts post_id to int immediately (rejects non-numeric input)
    Verifies post exists before touching likes table
    Checks if like row exists for this user + post combination
    If yes → DELETE (unlike)
    If no  → INSERT (like)
    Redirects back to dashboard (Post/Redirect/Get pattern)

### dashboard.php (query update)
The posts query was extended to include like data in the same
JOIN — no extra query per post:

    LEFT JOIN likes l ON l.post_id = p.id
    COUNT(l.id) AS like_count
    MAX(CASE WHEN l.user_id = :me THEN 1 ELSE 0 END) AS liked_by_me

LEFT JOIN ensures posts with zero likes are still returned.
MAX(CASE WHEN...) collapses all like rows per post into a single
1 or 0 indicating whether the current user has liked it.
GROUP BY required because of the COUNT and MAX aggregates.

### Optimistic UI (no page refresh)
Replaced the like <form> with a plain <button> using data attributes.
JavaScript intercepts the click, updates the UI instantly, then sends
fetch() in the background.

    data-post-id  → which post
    data-liked    → current state (1 or 0)
    data-count    → current like count

toggleLike() flow:
    1. Reads current state from data attributes
    2. Immediately flips UI (heart fills, count updates) — optimistic
    3. Sends POST to likeback.php via fetch()
    4. On network/server error → rolls back to original state

applyLikeState() handles all DOM updates in one place:
updates data attributes, CSS class, count span, word span,
and re-renders the Lucide heart so fill updates correctly.

## API Layer (/api/like.php)

### GET /api/like.php?post_id=X
Returns like status for the authenticated user on a specific post.

    200 → { post_id, like_count, liked_by_me }
    400 → Valid post_id required
    404 → Post not found

### POST /api/like.php?post_id=X
Toggles like on/off. Returns updated state after toggle.

    200 → { action, post_id, like_count, liked_by_me }
    action is either "liked" or "unliked"
    like_count reflects the new count after the toggle
    liked_by_me is derived from action === 'liked'
    400 → Valid post_id required
    404 → Post not found

## API Testing (Postman)
    GET /api/like.php?post_id=7 → returned like_count: 2, liked_by_me: false
    POST /api/like.php?post_id=7 → action: "liked", like_count: 3, liked_by_me: true
    POST /api/like.php?post_id=7 again → action: "unliked", like_count: 2, liked_by_me: false

## Key Takeaways
    UNIQUE KEY on (user_id, post_id) enforces like integrity at DB level
    Aggregating like data in the main posts JOIN avoids N+1 queries
    Optimistic UI makes interactions feel instant — roll back only on error
    fetch() can call a redirect-based PHP handler without caring about
    the response body — the redirect just gets followed silently

## 🖼️ Screenshots (Day 16)

### Feed — unliked state (user 1)
![unliked state](./screenshots1/Screenshot_2026-05-04_16-06-08.png)

### Feed — liked state (user 2, no refresh)
![liked state optimistic](./screenshots1/Screenshot_2026-05-04_16-06-39.png)

### GET /api/like.php?post_id=7
![GET like status](./screenshots1/Screenshot_2026-05-04_16_20_23.png)

### POST /api/like.php?post_id=7 — liked
![POST like](./screenshots1/Screenshot_2026-05-04_16_21_08.png)

### POST /api/like.php?post_id=7 — unliked
![POST unlike](./screenshots1/Screenshot_2026-05-04_16_21_42.png)

# Day 17 – Comments: Session Layer

## Overview

On Day 17, I added the comments feature to the session layer of MiniSocialApp.
Comments are expandable under each post — hidden by default, revealed on click
with no page reload, and auto-open after submitting or deleting a comment.

➡️ commentback.php (new)
➡️ dashboard.php (updated)

## Database

### comments table
    id         → auto-increment primary key
    post_id    → FK referencing posts(id), CASCADE on delete
    user_id    → FK referencing users(id), CASCADE on delete
    content    → VARCHAR(500), cannot be empty
    created_at → auto-set on insert

    INDEX on post_id   → speeds up fetching comments per post
    INDEX on created_at → speeds up chronological ordering
    Both FKs CASCADE on delete — no orphan comments if post or user is removed

## dashboard.php — What Changed

### Query update
The main posts query was extended with a second LEFT JOIN:

    LEFT JOIN comments c ON c.post_id = p.id
    COUNT(DISTINCT c.id) AS comment_count

DISTINCT is now required on both COUNT(l.id) and COUNT(c.id). With two
LEFT JOINs on the same post, rows multiply — a post with 3 likes and 2
comments produces 6 joined rows. Without DISTINCT, like_count would return
6 instead of 3. DISTINCT collapses them back to accurate counts.

### Second query — all comments fetched at once
A separate query fetches every comment for every post in one call,
joined with users to get author info, ordered oldest first so threads
read top to bottom chronologically.

Comments are then grouped by post_id in PHP into $commentsByPost so
each post can look up its comments instantly during the loop — no
extra DB query per post (avoids N+1 problem).

### open_comments session variable
After a comment is created or deleted, commentback.php stores the
post_id in $_SESSION['open_comments']. When dashboard reloads after
the redirect, it reads this value and adds the open CSS class to that
post's comment section so it's automatically expanded — the user sees
their comment or the deletion result without having to click again.
The value is cleared immediately after reading so it only fires once.

### Comment button in the post footer
Added alongside the like button:

    <button class="comment-btn" onclick="toggleComments(postId)">
        <i data-lucide="message-circle"></i>
        N Comments
    </button>

Displays the comment count from the query. Calls toggleComments() on click.

### Expandable comments section
Hidden by default (display:none). Gets the open class from toggleComments()
or from the open_comments session variable after a redirect.

Contains:
    All existing comments for the post (avatar, name, time, content, delete X)
    Delete button visible only to the comment owner or an admin
    Confirm dialog before deletion
    Comment input form at the bottom (text input + Send button)

### toggleComments() JS function
    classList.toggle('open') — one call handles both expand and collapse
    After opening, focuses the comment input after 50ms delay — needed
    because browsers won't focus an element that was just switched from
    display:none without a short timeout

## commentback.php

Single handler for two actions via hidden form field.

### create_comment
    Casts post_id to int, trims content whitespace
    Validates content is not empty after trim
    Validates content is under 500 characters
    Verifies the post still exists before inserting
    Inserts row into comments table
    Sets $_SESSION['open_comments'] = $postId so section auto-opens
    Redirects back to dashboard (Post/Redirect/Get pattern)

### delete_comment
    Fetches comment first to get user_id and post_id
    Server-side ownership check — self or admin only
    Deletes the row
    Sets $_SESSION['open_comments'] = $comment['post_id']
    Redirects back to dashboard

## Key Takeaways
    COUNT(DISTINCT) is required when you have multiple LEFT JOINs on
    the same table — without it joined rows multiply and counts are wrong
    Fetching all comments in one query then grouping in PHP is always
    better than one query per post inside the loop
    The open_comments session variable keeps UX smooth after redirects —
    the user always lands with their comment visible
    50ms focus delay after display:none toggle is a real browser quirk

## 🖼️ Screenshots (Day 17 — Session Layer)

### Post card with comment feature added
![post card with comment button](./screenshots1/Screenshot_2026-07-16_10-56-16.png)

### Expanded section — no comments yet
![expanded empty state](./screenshots1/Screenshot_2026-07-16_10-56-53.png)

### One comment added
![one comment](./screenshots1/Screenshot_2026-07-16_10-58-08.png)

### Three comments from different users
![three comments](./screenshots1/Screenshot_2026-07-16_11-13-42.png)

### Delete confirmation dialog
![delete confirm](./screenshots1/Screenshot_2026-07-16_11-16-34.png)

### Two comments remaining after deletion
![after deletion](./screenshots1/Screenshot_2026-07-16_11-17-09.png)

# Day 18 – Comments: API Layer

## Overview

On Day 18, I completed the comments feature by building the API layer —
mirroring everything the session layer already does but as clean REST
endpoints consumed via JWT authentication.

➡️ /api/comments.php (new)
➡️ /api/comment.php (new)

## Endpoints

### GET /api/comments.php?post_id=X — fetch all comments (public)

Returns all comments for a specific post with full author info.

Key Features:
    No authentication required
    Validates post_id is numeric
    Verifies post exists before querying comments
    JOIN with users table returns author_name and author_email
    Ordered oldest first (ASC) so thread reads chronologically
    Returns comment_count alongside the array
Response:
    200 → { post_id, comment_count, comments[] }
    400 → Valid post_id required
    404 → Post not found

### POST /api/comment.php — create comment (JWT required)

Creates a new comment on a post.

Key Features:
    Requires valid JWT token
    Accepts raw JSON body: { post_id, content }
    Trims content whitespace before validation
    Validates content is not empty
    Enforces 500 character limit
    Verifies post exists before inserting
    Returns comment_id and post_id on success
Response:
    201 → Comment posted successfully + comment_id + post_id
    400 → Validation errors
    401 → Unauthorized
    404 → Post not found
    500 → Insert failure

### DELETE /api/comment.php?id=X — delete comment (JWT required)

Deletes a comment by ID. Owner or admin only.

Key Features:
    Fetches comment before deleting to verify ownership
    Returns post_id alongside comment_id in response
    Owner can delete their own comments
    Admin can delete any comment (tested separately)
    Server-side check — not just UI-level restriction
Response:
    200 → Comment deleted successfully + comment_id + post_id
    400 → Valid comment ID required
    403 → No permission
    404 → Comment not found
    500 → Delete failure

## API Testing (Postman)

Login to get JWT token first:
    POST /api/login.php → 200 + token

GET all comments:
    GET /api/comments.php?post_id=6 → 200
    Returned comment_count: 3 with full author info per comment

POST new comment:
    POST /api/comment.php → 201
    Body: { "post_id": 6, "content": "Please, who can tell me where to get this course" }
    Returned comment_id: 4, post_id: 6

DELETE by owner (comment_id 4):
    DELETE /api/comment.php?id=4 → 200
    Token belongs to the comment author
    Returned comment_id: 4, post_id: 6

DELETE by admin (comment_id 2):
    DELETE /api/comment.php?id=2 → 200
    Token belongs to admin, not the comment author
    Returned comment_id: 2, post_id: 6

## Key Takeaways
    GET /api/comments.php is public — anyone can read comments,
    only authenticated users can write or delete
    The response always includes post_id so the client knows
    which post to update without needing extra state
    Two separate DELETE tests were needed — owner and admin —
    because the authorization logic has two distinct code paths
    that both need to be verified

## 🖼️ Screenshots (Day 18 — API Layer)

### POST /api/login.php — get JWT token
![login for token](./screenshots1/Screenshot_2026-07-16_15_04_31.png)

### POST /api/comment.php — create comment (201)
![create comment](./screenshots1/Screenshot_2026-07-16_15_13_13.png)

### GET /api/comments.php?post_id=6 — all comments
![get comments](./screenshots1/Screenshot_2026-07-16_15_19_32.png)

### DELETE /api/comment.php?id=4 — owner deletes own comment
![delete by owner](./screenshots1/Screenshot_2026-07-16_15_27_13.png)

### DELETE /api/comment.php?id=2 — admin deletes another user's comment
![delete by admin](./screenshots1/Screenshot_2026-07-16_15_31_45.png)

# Day 19 – Security Audit & Hardening

## Overview

On Day 19, instead of adding a new feature, I did a full security and
quality audit across the entire codebase — session layer, API layer,
and configuration files. This was in preparation for wrapping up the
project properly.

## Audit Checklist

Checked every file for:
    Debug code left in (var_dump, print_r, display_errors)
    Missing session/auth guards on any page
    Missing input validation on any endpoint
    Consistent password hashing (password_hash / password_verify)
    SQL injection safety (prepared statements throughout)
    File upload validation consistency between session and API layers
    Hardcoded credentials anywhere outside .env
    .gitignore coverage for sensitive files
    CORS configuration on the API

## Findings

### 🔴 Critical — Hardcoded Gmail App Password
email_config.php had the Gmail SMTP username and App Password written
directly in plain text. This file was NOT covered by .gitignore —
only .env, .htaccess, uploads, and PHPMailer-master were ignored.

Confirmed via git log that this file was committed on Day 4 and has
been in git history ever since. Anyone with repo access could see the
live credentials in that commit.

### 🟡 CORS wide open
Access-Control-Allow-Origin: * in api/config.php allows any website
to call the API. Acceptable during Postman-only testing, but flagged
for tightening once a real frontend domain exists.

### 🟢 Everything else — clean
    No debug code in any of my own files
    Every session page has session_start() + proper auth guard
    Every API endpoint has consistent requireAuth()/requireAdmin() usage
    No test/temp/backup files anywhere in the project
    Image upload validation identical across both layers
    Password hashing consistent everywhere
    .env and .htaccess properly gitignored

## Fixes Applied

### email_config.php
    Removed hardcoded Host, Username, and Password
    Now reads all three from $_ENV via load-env.php
    require_once used so loading .env twice (once in config.php,
    once here) causes no issue

### .env
    Added SMTP_HOST, SMTP_USERNAME, SMTP_PASSWORD, APP_URL
    Credentials now live only in the gitignored file

### Immediate action taken
    Regenerated the Gmail App Password — the old one is treated as
    compromised regardless of repo visibility, since it sat in git
    history for 15+ days of commits

## Key Takeaways
    A file can be excluded from .gitignore by omission just as easily
    as by mistake — always double check every config file individually,
    not just the ones you remember creating
    Credentials committed to git history stay there even after being
    removed from the current file — rotating the secret is the only
    real fix, not just deleting the line
    require_once makes shared setup files (like env loaders) safe to
    include from multiple entry points without side effects
    An audit day with zero new features is still real progress —
    hardening is part of shipping, not separate from it

## 🖼️ No screenshots this day
This was a code-level audit with no new UI or API behavior to
demonstrate — the fix is in the files themselves.

# Day 20 – Wrapping Up: End of Active Development

## Overview

Day 20 marks the end of active feature development on MiniSocialApp.
This project started as a way to learn backend fundamentals through
PHP sessions and grew into a full-stack social app with a parallel
REST API layer, built and documented day by day over 20 sessions
(with a break in the middle for school exams and other projects).

This entry is a full retrospective of what was built, not a new feature.

## The Journey — Day by Day

    Day 1   → Session-based login system (the absolute basics)
    Day 2   → Registration with password hashing
    Day 3   → Email verification (token-based)
    Day 4   → Real email delivery via PHPMailer + Gmail SMTP
    Day 5   → Forgot/reset password flow
    Day 6   → Full profile management (edit, change password, delete account)
    Day 7   → Selective dark mode (dashboard + profile only)
    Day 8   → Admin system with role-based access control
    Day 9   → API foundation — JWT authentication from scratch
    Day 10  → API register + login endpoints
    Day 11  → API user management endpoints (RBAC enforced)
    Day 12  → API verify-email, resend, forgot-password, reset-password
    Day 13  → Bug fixes — PHP scoping bug, JWT logout, admin role toggle
    Day 14  → Posts feature + full dashboard redesign (navbar + tabs)
    Day 15  → Posts API (GET, POST, DELETE) with pagination
    Day 16  → Likes — session + API + optimistic no-refresh UI
    Day 17  → Comments — session layer (expandable sections)
    Day 18  → Comments API layer
    Day 19  → Security audit and hardening
    Day 20  → This — closing the loop

## What The Final App Actually Does

### Authentication (both layers)
    Register, login, logout with JWT + server-side token blacklist
    Email verification with expiring tokens
    Forgot/reset password with silent lookup (no account enumeration)
    Resend verification

### User Management (both layers)
    View and edit profile (name, phone, bio, location, dob)
    Change password with current-password verification
    Delete own account
    Admin: view all users, toggle roles, delete any user

### Social Features (both layers)
    Create posts with optional image upload (MIME-validated, 5MB cap)
    Delete own posts (or any post as admin)
    Like/unlike posts — optimistic UI, no page reload
    Comment on posts — expandable sections, delete own comments (or any as admin)

### Quality & Security
    Every endpoint uses prepared statements — no SQL injection surface
    Password hashing via password_hash/password_verify throughout
    Server-side authorization checks on every owner/admin action
    Consistent MIME validation on every file upload path
    No hardcoded secrets — all credentials in .env
    No emojis — Lucide icon set throughout, consistent visual language
    Dark mode support across the whole authenticated experience

## What Was Learned (The Real Takeaway)

This project was never really about building a social app. It was
about internalizing the fundamentals that every backend system needs:

    Sessions vs tokens — and why they solve different problems
    Why PHP functions don't inherit global scope, and what that
    breaks silently until you understand it
    The Post/Redirect/Get pattern and why every form handler should use it
    N+1 queries and why COUNT(DISTINCT) matters with multiple JOINs
    Why a git-committed secret is compromised forever, not just until
    you delete the line
    The difference between "it works" and "it's safe to ship" —
    and why those are two separate questions

## What's Not Being Built (On Purpose)

Follows/followers, real-time notifications, post editing, comment
likes, search, and a full MVC refactor with a router were all
considered. None of them were missing pieces — they were directions
the project could have gone in, not gaps it needed filling. Recognizing
the difference between "more features" and "the point being made" is
its own lesson.

## What's Next

MiniSocialApp is feature-complete as a learning project and stays as
a reference — both a portfolio piece and a foundation to look back on.
The next project starts clean, applying everything from here:
proper MVC structure from day one, a real router, and the instinct to
audit before calling something done.

## 🖼️ No new screenshots
This entry is a retrospective — every feature shown here was already
documented on its respective day.