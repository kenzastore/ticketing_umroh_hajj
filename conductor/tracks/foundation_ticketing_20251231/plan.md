# Track Plan: Foundation & Core Ticketing (Request to PNR)

## Phase 1: Environment & Authentication
- [ ] Task: Initialize database schema (users, roles, agents).
- [ ] Task: Implement `db_connect.php` using PDO with MariaDB.
- [ ] Task: Create Login Page (UI with Bootstrap 5).
- [ ] Task: Implement Login Logic (Session management, password verification).
- [ ] Task: Implement Logout and Middleware (Access control for protected pages).
- [ ] Task: Create Role-based Dashboards (Placeholders for Admin, Finance, Monitor).
- [ ] Task: Conductor - User Manual Verification 'Environment & Authentication' (Protocol in workflow.md)

## Phase 2: Core Request Management
- [ ] Task: Initialize database schema for `requests` and `bookings`.
- [ ] Task: Create "New Request" Form (UI & Backend validation).
- [ ] Task: Implement "Request List" View (Filtering and pagination).
- [ ] Task: Create "Request Detail" View.
- [ ] Task: Conductor - User Manual Verification 'Core Request Management' (Protocol in workflow.md)

## Phase 3: Status Lifecycle & PNR
- [ ] Task: Implement Status Update Logic (Workflow transitions).
- [ ] Task: Add PNR Entry functionality (Update status to PNR_ISSUED).
- [ ] Task: Implement basic Audit Log for status changes.
- [ ] Task: Final MVP Track Verification (Request -> PNR flow).
- [ ] Task: Conductor - User Manual Verification 'Status Lifecycle & PNR' (Protocol in workflow.md)
