# Track Spec: Database Schema Alignment (Worksheet Adoption)

## Overview
Adapt the existing MariaDB schema to fully support the "Digitalisasi Ticketing Umroh & Haji" workflow, strictly adopting the structure of the "ELANG EMAS WORKSHEET" to ensure seamless transition and data integrity.

## Source of Truth
- **Schema Definition:** `conductor/reference/schema.sql` (Target Schema)
- **Business Logic:** `conductor/product.md` (Based on Proposal PDF)
- **Data Structure Reference:** `conductor/worksheet/ELANG_EMAS_WORKSHEET.xlsx`

## Schema Migration Strategy
The current schema (`database/schema.sql`) is minimal. The goal is to **replace** the domain tables with the worksheet-based structure while **retaining** the authentication/system tables (`users`, `roles`, `audit_logs`).

### Tables to Retain (System)
- `users`
- `roles`
- `audit_logs` (Needs update to reference new entity IDs)

### Tables to Replace/Add (Domain)
- **`agents`**: Update/Replace to include `skyagent_id` and match worksheet columns.
- **`corporates`**: New table for Corporate entities.
- **`booking_requests`**: Replaces `requests`.
- **`movements`**: Replaces `bookings`. Central table for the workflow.
- **`payment_report_lines`**: New table for Payment Report.
- **`invoices`**: New table for Invoice headers.
- **`invoice_flight_lines`**: New table for Invoice flight details.
- **`invoice_fare_lines`**: New table for Invoice fare details.
- **`flight_legs`**: Normalized flight segments for movements.
- **`booking_request_legs`**: Normalized flight segments for requests.
- **`documents`**: For file storage.

## Supporting Files (Context)
- `database/schema.sql`: Current database structure (Baseline).
- `conductor/reference/schema.sql`: Target database structure.
- `app/models/`: Existing PHP models (will need heavy refactoring/replacement).

## Acceptance Criteria
- [ ] `database/schema.sql` is updated to contain the merged schema (System + Worksheet Domain).
- [ ] All new tables from `conductor/reference/schema.sql` are present.
- [ ] `users`, `roles`, `audit_logs` are preserved.
- [ ] Relationships (Foreign Keys) are correctly defined.