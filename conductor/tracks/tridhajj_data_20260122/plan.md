# Plan: End-to-End Dummy Data (TRID-HAJJ 2026)

## Phase 1: Foundation & Verification
Establish the testing framework and master data required for the storyline.

- [x] Task: Create Seeder Verification Test - Write `tests/TridHajjSeederTest.php` to verify that the seeder produces correct counts and linked records across all modules. [pass]
- [x] Task: Master Data Setup - Implement the initial part of `database/seed_uat_trid_hajj.php` to ensure the Agent, Corporate, and any required system users exist. [pass]
- [x] Task: Conductor - User Manual Verification 'Foundation & Verification' (Protocol in workflow.md) [pass]

## Phase 2: Storyline Implementation
Populate the database with the synchronized lifecycle data.

- [x] Task: Seed Request & Movement - Implement logic to create the initial Dec 2025 Booking Request and its conversion into the active Movement with 4 flight segments. [pass]
- [x] Task: Seed Financials - Implement logic to create the Invoice, dual-table Payment Report lines (Sales/Cost), and actual Payment records for Deposit 1. [pass]
- [x] Task: Seed Audit Logs - Add chronological log entries to simulate the history of the group from request to ticketing done. [pass]
- [x] Task: Conductor - User Manual Verification 'Storyline Implementation' (Protocol in workflow.md) [pass]

## Phase 3: Final Validation
Perform end-to-end checks to ensure the data is visible and accurate.

- [x] Task: Integrated Verification - Execute the seeder and run the verification test suite. [pass]
- [x] Task: Visual Alignment Check - Manually verify the Payment Report UI using PNR "VFUQ8X" to ensure a 1:1 match with the PDF reference. [pass]
- [x] Task: Conductor - User Manual Verification 'Final Validation' (Protocol in workflow.md) [pass]
