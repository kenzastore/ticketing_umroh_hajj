# Track Spec: AUDIT-001 Audit Log & Model Integrity Enhancement

## Overview
This track focuses on bringing the project into compliance with the core workflow mandates defined in `AGENTS.md`. It implements mandatory audit logging across all domain models to ensure data traceability and initializes the formal PHPUnit testing suite to satisfy project quality gates.

## Functional Requirements

### 1. Mandatory Audit Logging
- **Integrated Models:** `Agent`, `Corporate`, `BookingRequest`, `Movement`, and `Invoice`.
- **Captured Actions:** 
    - `CREATE`: When a new record is inserted.
    - `UPDATE`: When any field in an existing record is changed.
    - `DELETE`: When a record is removed (if soft-delete is not used).
    - `STATUS_CHANGE`: Specific to `Invoice` and `Movement`.
- **Data Integrity:** The `old_value` and `new_value` columns in the `audit_logs` table must store a **full JSON snapshot** of the record before and after the transaction respectively.

### 2. Testing Suite Initialization
- **Framework:** Setup and configure **PHPUnit**.
- **Configuration:** Create `phpunit.xml` and ensure it integrates with the project's autoloader.
- **Database Isolation:** Implement a strategy for testing database interactions (e.g., using a test database or transactional rollbacks).

### 3. Model Integrity Verification
- Write initial unit tests for each core model.
- Tests must verify:
    - Data correctly persists to MariaDB.
    - `AuditLog::log` is triggered with the correct parameters and JSON payload upon data modification.

## Technical Requirements
- Update model method signatures (e.g., `create`, `update`) to accept an optional `$userId` for audit logging purposes.
- Ensure `json_encode` is used for snapshotting to maintain the `audit_logs` schema requirements.

## Acceptance Criteria
- [ ] `AuditLog::log` is successfully integrated into all specified models.
- [ ] Modifying any record via the models generates an entry in the `audit_logs` table with full snapshots.
- [ ] PHPUnit is installed and configured (running `vendor/bin/phpunit` executes successfully).
- [ ] Unit tests for all 5 core models pass, specifically verifying the audit trail.
- [ ] Code coverage for the core models exceeds the 80% threshold.

## Out of Scope
- Frontend UI for viewing or searching audit logs.
- Refactoring the models beyond adding logging and testing support.
