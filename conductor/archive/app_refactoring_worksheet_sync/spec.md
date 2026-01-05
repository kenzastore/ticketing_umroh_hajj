# Track Spec: App Refactoring (Worksheet Sync)

## Overview
Update the PHP application layer (Models and Public controllers) to interact with the new consolidated schema. This ensures the web interface functions correctly with the worksheet-based data structure.

## Scope of Changes

### 1. Models (`app/models/`)
- **`Agent.php`**: Update to include `skyagent_id` and remove old address/contact fields if they don't exist in the new schema.
- **`Corporate.php`**: New model for the `corporates` table.
- **`BookingRequest.php`**: New model for the `booking_requests` table (replaces old request logic).
- **`Movement.php`**: New model for the `movements` table (replaces `Booking.php`).
- **`Invoice.php`**: Refactor to use new `invoices`, `invoice_flight_lines`, and `invoice_fare_lines` tables.
- **`PaymentReport.php`**: New model for the `payment_report_lines` table.

### 2. Public Controllers / UI (`public/admin/`)
- **`new_request.php` / `new_request_process.php`**: Update to use `BookingRequest` and `booking_requests` table.
- **`movement_fullview.php`**: Update to pull from the `movements` table and support the new column structure (DP1, DP2, FP, etc.).
- **`request_detail.php`**: Update to handle new request schema.
- **`finance/create_invoice.php`**: Update to handle the 2-version invoice logic and new table structure.

## Context & Constraints
- Keep `db_connect.php` as is.
- Ensure `AuditLog::log` is called for all create/update/delete operations.
- Maintain consistency with the worksheet column names (e.g., `skyagent_id`, `pnr`, `tour_code`).

## Acceptance Criteria
- [ ] All new models are implemented and correctly map to the `database/schema.sql`.
- [ ] Existing functionality (Request, Movement, Invoice) is restored and functional.
- [ ] No references to deleted tables (`requests`, `bookings`) remain in the code.
