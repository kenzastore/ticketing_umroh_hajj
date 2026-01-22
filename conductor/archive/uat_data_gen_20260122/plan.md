# Plan: UAT Dummy Data Generation System

## Phase 1: Seeder Engine Development [checkpoint: e205941]
Build the core CLI-based seeding engine that handles the complex relational synchronization.

- [x] Task: Write Seeder Logic Tests - Create `tests/UatSeederLogicTest.php` to verify synchronization rules (e.g., Request to Movement pax match). [pass]
- [x] Task: Implement Seeder Engine - Create `database/seed_uat_system.php` with logic for 100 records and workflow distribution (25/25/25/25). [pass]
- [x] Task: Implement Data Cleanup - Add the "Wipe and Replace" logic to the seeder to clear existing operational tables. [pass]
- [x] Task: Verify CLI Seeder - Run the seeder manually and check record counts in the database. [pass]
- [x] Task: Conductor - User Manual Verification 'Phase 1: Seeder Engine Development' (Protocol in workflow.md) [pass]

## Phase 2: Integration & UI Trigger [checkpoint: e205941]
Connect the seeding engine to the web interface via a secure admin trigger.

- [x] Task: Create UI Reset Tests - Write integration tests to ensure the reset link is only visible to admins. [pass]
- [x] Task: Implement Backend Trigger - Create `public/admin/generate_uat_data.php` to execute the seeder script via shell. [pass]
- [x] Task: Add UI Link to Manual - Update `public/shared/manual.php` to include the "Regenerate UAT Data" link in the sidebar for admins. [pass]
- [x] Task: Conductor - User Manual Verification 'Phase 2: Integration & UI Trigger' (Protocol in workflow.md) [pass]

## Phase 3: Workflow & Traceability Validation [checkpoint: e205941]
Ensure the generated data correctly simulates real-world UAT scenarios and audit trails.

- [x] Task: Verify Distribution & States - Validate that all 4 lifecycle stages are represented correctly in the dashboard and lists. [pass]
- [x] Task: Verify Audit Log Integrity - Confirm that each of the 100 records has a consistent chronological trail in `audit_logs`. [pass]
- [x] Task: Role Visibility Check - Perform a final walkthrough as different roles (Finance vs. Operational) to ensure data filtering works. [pass]
- [x] Task: Conductor - User Manual Verification 'Phase 3: Workflow & Traceability Validation' (Protocol in workflow.md) [pass]
