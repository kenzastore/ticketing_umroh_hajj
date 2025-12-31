# Track Plan: Foundation & Core Ticketing (Request to PNR)

## Phase 1: Environment ## Phase 1: Environment & Authentication Authentication [checkpoint: ef7c9a7]
- [x] Task: Initialize database schema (users, roles, agents). [cecd4f9]
- [x] Task: Implement `db_connect.php` using PDO with MariaDB. [a81df13]
- [x] Task: Create Login Page (UI with Bootstrap 5). [c65ef37]
- [x] Task: Implement Login Logic (Session management, password verification). [c0bc03a]
- [x] Task: Implement Logout and Middleware (Access control for protected pages). [6b6d890]
- [x] Task: Create Role-based Dashboards (Placeholders for Admin, Finance, Monitor). [07c85de]
- [x] Task: Conductor - User Manual Verification 'Environment & Authentication' (Protocol in workflow.md)

## Phase 2: Core Request Management
- [x] Task: Initialize database schema for `requests` and `bookings`. [b988df7]
- [x] Task: Create "New Request" Form (UI & Backend validation). [eb11e1c]
- [x] Task: Implement "Request List" View (Filtering and pagination). [35349ad]
- [x] Task: Create "Request Detail" View. [0346c3c]
- [x] Task: Conductor - User Manual Verification 'Core Request Management' (Protocol in workflow.md)

## Phase 3: Status Lifecycle & PNR
- [ ] Task: Implement Status Update Logic (Workflow transitions).
- [ ] Task: Add PNR Entry functionality (Update status to PNR_ISSUED).
- [ ] Task: Implement basic Audit Log for status changes.
- [ ] Task: Final MVP Track Verification (Request -> PNR flow).
- [ ] Task: Conductor - User Manual Verification 'Status Lifecycle & PNR' (Protocol in workflow.md)
