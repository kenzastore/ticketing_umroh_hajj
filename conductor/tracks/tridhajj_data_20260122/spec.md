# Specification: End-to-End Dummy Data (TRID-HAJJ 2026)

## 1. Overview
This track provides a comprehensive PHP seeding script to populate the database with a realistic, end-to-end business scenario based on the `TRID-HAJJ_1447H-2026M.pdf` reference. The data will span all major modules—from the initial Booking Request to the active Movement, Financial Settlement (Invoicing/Payments), and Audit Logging—ensuring a perfectly synchronized state for UAT and system demonstration.

## 2. Functional Requirements

### 2.1 Master Data Setup
- Create/Verify Agent: **EBAD WISATA**.
- Create/Verify Corporate: **EBAD WISATA** (if treated as a corporate client).
- Create/Verify Bank Accounts: Ensure "PT ELANG (MANDIRI)" and "FLYSCOOT" are available in the master references if applicable.

### 2.2 Stage 1: Demand Intake (Booking Request)
- **Request Date:** 2025-12-01.
- **Pax:** 45 Adult.
- **Program:** 26 Days.
- **Status:** Marked as converted to Movement.

### 2.3 Stage 2: Operational Execution (Movement)
- **PNR:** VFUQ8X.
- **Tour Code:** 11MAY-45S-26D-TRID.
- **Flight Legs (4 Segments):**
    1. 2026-05-11: SUB-SIN (TR297) 10:35-14:00.
    2. 2026-05-11: SIN-JED (TR596) 16:05-20:35.
    3. 2026-06-04: JED-SIN (TR597) 21:50-12:35+1.
    4. 2026-06-05: SIN-SUB (TR266) 17:15-18:35.
- **Ticketing Status:** Set to "DONE".

### 2.4 Stage 3: Financial Settlement
- **Invoice:** Generate a Proforma Invoice matching the sales fare (10,000,000/pax).
- **Payment Report Lines (Table 1 - Sales):**
    - Selling Fare: 450,000,000.
    - Deposit 1 (20%): 90,000,000 (Status: PAID, Date: 2026-01-09).
    - Deposit 2 & 3: Pending with time limits (2026-01-23, 2025-04-06).
- **Payment Report Lines (Table 2 - Cost):**
    - Nett Fare: 445,500,000 (9,900,000/pax).
    - Deposit 1 (Cost): 89,100,000 (Status: PAID, Date: 2026-01-12).
- **Payments:** Record actual transaction entries for the paid deposits to ensure balance consistency.

### 2.5 Stage 4: Traceability (Audit Logs)
- Generate log entries for:
    - Request Creation (2025-12-01).
    - Conversion to Movement (2025-12-31).
    - Payment recording for Deposit 1.

## 3. Acceptance Criteria
- [ ] Running `php database/seed_uat_trid_hajj.php` completes without errors.
- [ ] PNR "VFUQ8X" is visible in the Movement Dashboard with all 4 legs correctly timed.
- [ ] The Payment Report for this PNR shows the dual-table layout with a 4,500,000 IDR Incentive.
- [ ] Audit logs show a clear chronological history of this group's lifecycle.

## 4. Out of Scope
- Automated UI tests (this track focuses on data provisioning).
- Cleanup of other unrelated dummy data.
