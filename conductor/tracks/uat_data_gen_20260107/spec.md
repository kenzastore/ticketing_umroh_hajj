# Track Spec: Generate Indonesian Dummy Data for UAT

## Overview
This track involves the creation of 100 high-quality dummy records tailored for the Indonesian Umroh/Hajj market. The goal is to provide a realistic and interconnected dataset to support comprehensive UAT (User Acceptance Testing) scenarios, covering the entire workflow from Master Data to Invoicing.

## Functional Requirements

### 1. Data Generation Scope (100 Records Total)
- **Master Data (10 records):** 5 Indonesian Travel Agents (e.g., "Mutiara Tour", "Amanah Wisata") and 5 Corporates.
- **Booking Requests (30 records):** 
    - Realistic Indonesian names and groups.
    - Varied group sizes (5 to 120 pax).
    - Status mix (Draft, Converted).
- **Movements (40 records):**
    - Linked to Booking Requests where applicable.
    - Diverse flight legs (1-4 segments).
    - Realistic PNRs and Tour Codes.
    - Indonesian regional airports (SUB, CGK, JOG, etc.) and destination airports (JED, MED).
- **Invoices & Payments (20 records):**
    - Linked to Movements.
    - Mix of payment statuses (UNPAID, PARTIALLY_PAID, PAID).

### 2. Testing Scenarios / Edge Cases
- **H-3 Reminders:** At least 5 records per category (Ticketing, DP1, DP2, FP) set to expire in exactly 3 days.
- **Past Due:** At least 5 records with expired deadlines and incomplete statuses.
- **Payment Mix:** Records reflecting different stages of the DP1 -> DP2 -> FP lifecycle.

### 3. Localization
- **Names:** Use common Indonesian personal and business names.
- **Currency/Values:** Use realistic IDR values for fares and payments.

## Technical Requirements
- **Implementation:** A new PHP seeding script located at `database/seed_uat_data.php`.
- **Methodology:** Use the existing Model classes (`Agent`, `Corporate`, `BookingRequest`, `Movement`, `Invoice`, `Payment`) to ensure data integrity and audit log generation.
- **Cleanup:** The script should provide an option or clear instruction to clear existing data before seeding to ensure a clean UAT state.

## Acceptance Criteria
- [ ] Running `php database/seed_uat_data.php` successfully populates the database with 100 interconnected records.
- [ ] The dashboard "Urgent Deadlines" widget correctly displays the seeded H-3 and Past Due records.
- [ ] All seeded data follows the Indonesian localized naming and airport conventions.
- [ ] Audit logs are correctly generated for all created records.
