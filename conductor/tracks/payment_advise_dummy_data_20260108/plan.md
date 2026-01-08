# Plan: Dummy Data Generation for Payment Advice UAT

## Phase 1: Analysis and Test Preparation [checkpoint: 8b3d4ef]
- [x] Task: Analyze `database/seed_uat_data.php` and `app/models/PaymentAdvise.php` to identify insertion points and required fields. <!-- 164c51a -->
- [x] Task: Create a unit test `tests/PaymentAdviseSeedingTest.php` to define and verify the expected seeded data. <!-- 81bb298 -->
- [x] Task: Conductor - User Manual Verification 'Analysis and Test Preparation' (Protocol in workflow.md) <!-- 8b3d4ef -->

## Phase 2: Integrated Seeding Implementation
- [ ] Task: Update `database/seed_uat_data.php` to include a Payment Advice seeding section.
- [ ] Task: Implement logic to link new Payment Advises to existing UAT Movements.
- [ ] Task: Ensure seeding is idempotent (e.g., check for existing records before insertion).
- [ ] Task: Conductor - User Manual Verification 'Integrated Seeding Implementation' (Protocol in workflow.md)

## Phase 3: Final Verification
- [ ] Task: Execute the full `database/seed_uat_data.php` script and verify data presence in the database.
- [ ] Task: Manually verify the visibility and accuracy of dummy data in the Payment Advice List UI.
- [ ] Task: Conductor - User Manual Verification 'Final Verification' (Protocol in workflow.md)
