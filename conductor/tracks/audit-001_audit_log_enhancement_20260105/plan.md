# Track Plan: AUDIT-001 Audit Log & Model Integrity Enhancement

This plan details the steps to implement mandatory audit logging across core models and initialize the PHPUnit testing suite.

## Phase 1: Testing Infrastructure & Framework Setup [checkpoint: 09e891b]
- [x] Task: Install PHPUnit via composer (Completed using PHAR due to Composer 1 issues). [09e891b]
- [x] Task: Create `phpunit.xml` configuration file at the project root. [09e891b]
- [x] Task: Create a base test class or trait for database isolation (transactional rollbacks). [09e891b]
- [x] Task: Conductor - User Manual Verification 'Testing Infrastructure' (Protocol in workflow.md) [09e891b]

## Phase 2: Audit Logging - Master Data (Agent & Corporate) [checkpoint: 36f6b94]
- [x] Task: Write failing unit tests for `Agent.php` and `Corporate.php` verifying `AuditLog` triggers. [36f6b94]
- [x] Task: Implement `AuditLog::log` in `Agent::create`, `Agent::update`, and `Agent::delete`. [36f6b94]
- [x] Task: Implement `AuditLog::log` in `Corporate::create`, `Corporate::update`, and `Corporate::delete`. [36f6b94]
- [x] Task: Verify tests pass and check JSON snapshots in the `audit_logs` table. [36f6b94]
- [x] Task: Conductor - User Manual Verification 'Master Data Audit' (Protocol in workflow.md) [36f6b94]

## Phase 3: Audit Logging - Core Workflow (BookingRequest & Movement) [checkpoint: 431eca8]
- [x] Task: Write failing unit tests for `BookingRequest.php` and `Movement.php` verifying `AuditLog` triggers. [431eca8]
- [x] Task: Implement `AuditLog::log` in `BookingRequest` (Create/Update/Delete). [431eca8]
- [x] Task: Implement `AuditLog::log` in `Movement` (Create/Update/Delete/Status Change). [431eca8]
- [x] Task: Verify tests pass and ensure full JSON snapshots are captured. [431eca8]
- [x] Task: Conductor - User Manual Verification 'Workflow Audit' (Protocol in workflow.md) [431eca8]

## Phase 4: Audit Logging - Financial (Invoice) [checkpoint: 60d5032]
- [x] Task: Write failing unit tests for `Invoice.php` verifying `AuditLog` triggers. [60d5032]
- [x] Task: Implement `AuditLog::log` in `Invoice::create` and status updates. [60d5032]
- [x] Task: Verify tests pass and ensure snapshots include related lines if necessary. [60d5032]
- [x] Task: Conductor - User Manual Verification 'Financial Audit' (Protocol in workflow.md) [60d5032]

## Phase 5: Final Quality Gate & Documentation
- [ ] Task: Run full test suite and verify >80% code coverage for the 5 core models.
- [ ] Task: Update `conductor/tech-stack.md` to include PHPUnit as the testing framework.
- [ ] Task: Conductor - User Manual Verification 'Final Quality Gate' (Protocol in workflow.md)
