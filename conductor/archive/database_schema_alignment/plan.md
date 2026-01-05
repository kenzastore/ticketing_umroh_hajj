# Track Plan: Database Schema Alignment (Worksheet Adoption)

This plan outlines the steps to migrate the MariaDB schema to the "Worksheet-based Schema".

## Phase 1: Schema Consolidation
1.  **Read Source Schemas:**
    - Read `database/schema.sql` (Current).
    - Read `conductor/reference/schema.sql` (Target).
2.  **Create Merged Schema File:**
    - Create a new SQL file that:
        - Keeps `roles`, `users`, `audit_logs` from current schema.
        - Includes all tables from `conductor/reference/schema.sql` (`agents`, `corporates`, `booking_requests`, `movements`, etc.).
        - ensure `agents` table is handled correctly (drop old or alter). Given the significant difference, `DROP TABLE IF EXISTS agents` followed by the new definition is cleaner, provided we don't need to migrate data (assuming dev/greenfield).
3.  **Apply Schema:**
    - Overwrite `database/schema.sql` with the new merged content.
    - Run the SQL to update the local database (if applicable/requested).

## Phase 2: Codebase Impact Assessment
1.  **Identify Model Breakers:**
    - `Request.php` (if exists) -> will break (table `requests` gone).
    - `Booking.php` (if exists) -> will break (table `bookings` gone).
    - `Agent.php` -> fields changed.
    - `Invoice.php` / `Payment.php` -> fields changed/tables renamed.
2.  **Refactor Plan (Subsequent Tracks):**
    - This track focuses on the *Database Layer*.
    - Immediate follow-up action: Create a "Model Refactoring" track to update PHP classes to match the new schema.

## Steps
- [ ] Generate consolidated `database/schema.sql`.
- [ ] Verify syntax of the new schema file.
- [ ] (Optional) Execute schema against local DB to verify constraints.