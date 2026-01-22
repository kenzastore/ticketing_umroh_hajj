# Specification: Dummy Data for Payment Report Alignment (TRID-HAJJ)

## 1. Overview
This track provides a specialized PHP seeding script to populate the database with a complete, realistic scenario based on the `TRID-HAJJ_1447H-2026M.pdf` reference. This data will be used to verify the dual-table layout, bank details, and incentive calculations implemented in the Payment Report Alignment track.

## 2. Functional Requirements

### 2.1 Seeding Script (`database/seed_payment_report_trid.php`)
- Create a standalone PHP script that connects to the database and performs the following:
    - **Movement Creation:** Insert a movement record for Agent "EBAD WISATA" with PNR "VFUQ8X" and Tour Code "11MAY-45S-26D-TRID".
    - **Flight Legs:** Insert the 4 legs (SUB-SIN, SIN-JED, JED-SIN, SIN-SUB) with exact dates and flight numbers from the PDF.
    - **Sales Lines (Table 1):** Insert "SELLING FARE" and 3 "DEPOSIT" lines with the correct amounts and "SALES" table type.
    - **Cost Lines (Table 2):** Insert "NETT FARES" and 3 "DEPOSIT" lines with the correct amounts and "COST" table type.
    - **Bank Details:** Include the specific bank names and account numbers provided in the PDF (e.g., MANDIRI, SCOOT, PT ELANG).
    - **Summary Fields:** Set `incentive_amount` to 4,500,000 and `discount_amount` to 0 in the movements table.

### 2.2 Data Integrity
- Ensure all IDs are correctly linked (Movement -> Legs, PNR -> Report Lines).
- Use `INSERT IGNORE` or check for existing data to prevent duplicate primary key errors if the script is run multiple times.

## 3. Acceptance Criteria
- [ ] Running `php database/seed_payment_report_trid.php` completes without errors.
- [ ] A new movement with PNR "VFUQ8X" appears in the system.
- [ ] Opening the Payment Report for this movement shows two tables that visually match the PDF reference.
- [ ] All bank account numbers and time limits are correctly displayed in the UI.

## 4. Out of Scope
- Integration tests for the UI (this track is strictly for data provisioning).
- Cleanup of existing production data (the script targets a specific test PNR).
