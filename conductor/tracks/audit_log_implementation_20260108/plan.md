# Plan: Audit Log Implementation

## Phase 1: Core Audit Log Mechanism [checkpoint: f4243d5]
- [x] Task: Create/Verify `audit_logs` table schema and `app/models/AuditLog.php` model. [ef63d36]
- [x] Task: Implement `AuditLog::log()` method to store full payload diffs (old/new values) for given actions. [17194cf]
- [x] Task: Write unit tests for `AuditLog::log()` to ensure correct data storage and diffing. [17194cf]
- [ ] Task: Conductor - User Manual Verification 'Core Audit Log Mechanism' (Protocol in workflow.md)

## Phase 2: Integrate Audit Logging into Modules
- [ ] Task: Implement audit logging for **User Management** actions (create/update/delete User, change Role).
- [ ] Task: Implement audit logging for **Booking Request** actions (create/update/delete Request, Convert to Movement).
- [ ] Task: Implement audit logging for **Movement Monitoring** actions (update PNR/Tour Code, status updates, Time Limit changes).
- [ ] Task: Implement audit logging for **Financial Operations** actions (Invoice create/void, Payment record, Payment Advice create/update).
- [ ] Task: Write integration tests to verify logs are generated for each auditable action.
- [ ] Task: Conductor - User Manual Verification 'Integrate Audit Logging into Modules' (Protocol in workflow.md)

## Phase 3: Audit Log User Interface
- [ ] Task: Create `public/admin/audit_logs.php` to display audit logs with pagination and filtering.
- [ ] Task: Implement filtering by user, entity type, action type, and date range on the Audit Log page.
- [ ] Task: Implement Role-Based Access Control (RBAC) to ensure only Admin users can access `public/admin/audit_logs.php`.
- [ ] Task: Write UI/integration tests for the Audit Log display and filtering functionality.
- [ ] Task: Conductor - User Manual Verification 'Audit Log User Interface' (Protocol in workflow.md)

## Phase 4: Final Validation
- [ ] Task: Perform end-to-end testing to ensure all specified actions correctly trigger audit logs visible in the UI.
- [ ] Task: Verify that audit log entries correctly capture old and new values in JSON format.
- [ ] Task: Conductor - User Manual Verification 'Final Validation' (Protocol in workflow.md)
