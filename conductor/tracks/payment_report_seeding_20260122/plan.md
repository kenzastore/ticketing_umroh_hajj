# Plan: Dummy Data for Payment Report Alignment (TRID-HAJJ)

## Phase 1: Seeding Implementation [checkpoint: a49cc3b]
Establish the specialized seeding script and verify its data output.

- [x] Task: Write Seeder Verification Test - Create `tests/PaymentReportSeederTest.php` to verify that the seeder creates the expected movement and accounting records. [pass]
- [x] Task: Create Seeding Script - Implement `database/seed_payment_report_trid.php` with Movement (PNR: VFUQ8X), 4 Flight Legs, and complete Sales/Cost report lines. [pass]
- [x] Task: Verify Visual Alignment - Execute the seeder and perform a manual check of the Payment Report UI to ensure it matches the TRID-HAJJ PDF reference. [pass]
- [x] Task: Conductor - User Manual Verification 'Phase 1: Seeding Implementation' (Protocol in workflow.md) [pass]
