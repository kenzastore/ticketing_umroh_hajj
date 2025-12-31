# Track Spec: Foundation & Core Ticketing (Request to PNR)

## Overview
This track implements the foundational user authentication system and the core lifecycle of a ticketing request. It covers everything from capturing a new request to issuing a PNR code.

## User Stories
- **As a User**, I want to log in securely so that I can access the system based on my role.
- **As a Ticketing Admin**, I want to capture new Group/Individual requests with schedule and airline preferences.
- **As a Ticketing Admin**, I want to track the status of a request from NEW to QUOTED, BLOCKED, and finally PNR_ISSUED.
- **As a User**, I want to be redirected to a dashboard suitable for my role after logging in.

## Functional Requirements
- **Authentication:**
  - Login form with username/password.
  - Session management for logged-in users.
  - Role-based redirection (Admin, Finance, Monitor).
- **Request Management:**
  - Database schema for `users`, `agents`, `requests`, and `bookings`.
  - Form to create new requests (Date, Pax, Airline, Agent/FID).
  - List view of all requests with filtering by status.
- **Status Lifecycle:**
  - Ability to update status through the workflow: `NEW` -> `QUOTED` -> `BLOCKED` -> `PNR_ISSUED`.
  - Specific input for PNR code that triggers status update.

## Technical Requirements
- **Language:** Native PHP.
- **Database:** MariaDB (Prepared statements mandatory).
- **Frontend:** Bootstrap 5.
- **Security:** CSRF protection for forms, password hashing (bcrypt).

## Acceptance Criteria
- User can log in and see their specific dashboard.
- A new request can be created and appears in the list.
- A request status can be successfully updated to `PNR_ISSUED` when a PNR code is provided.
- Unauthenticated users cannot access internal pages.
