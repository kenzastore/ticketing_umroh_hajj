# Specification: Audit Log Implementation

## 1. Overview
This track focuses on implementing a comprehensive Audit Log functionality to track critical user actions across key modules of the Ticketing Umroh & Haji system. The goal is to enhance accountability, data integrity, and provide a historical record of changes.

## 2. Functional Requirements
### 2.1 Audit Log Recording
The system must record audit logs for the following modules/actions:
- **User Management:** Login/Logout, User creation/update/deletion, Role changes.
- **Booking Requests:** Creation, Updates (e.g., group size, flight details), Deletion, Conversion to Movement.
- **Movement Monitoring:** PNR/Tour Code changes, Status updates (DP1, DP2, FP, Ticketing Done), Time Limit adjustments.
- **Financial Operations:** Invoice generation/voiding, Payment recording, Payment Advice creation/update.

### 2.2 Detail Level
- For each auditable action, the system must capture and store the **full payload difference**, recording both the old and new state of the entity in JSON format. This provides a detailed history of "what changed from what to what."
- Key information to be logged: User performing the action, timestamp, entity type, entity ID, action type (CREATE, UPDATE, DELETE, STATUS_CHANGE), old value (JSON), new value (JSON).

### 2.3 Audit Log Visibility
- A **dedicated Admin Page** must be implemented to display all audit logs. This page should include:
    - Filtering capabilities (by user, entity type, action type, date range).
    - Pagination for large volumes of logs.
    - A clear, human-readable display of the logged information, including the payload differences.

### 2.4 Access Control
- Only **Admin** users should have full access to view all audit logs.

## 3. Non-Functional Requirements
- **Performance:** Audit log recording should not significantly impact the performance of core application functions.
- **Security:** Audit logs must be immutable (cannot be altered after creation) and protected from unauthorized access.
- **Storage:** Consider efficient storage for JSON payloads, potentially text fields.

## 4. Acceptance Criteria
- [ ] Critical actions in User Management, Booking Requests, Movement Monitoring, and Financial Operations trigger audit log entries.
- [ ] Audit log entries contain full payload differences (old_value and new_value JSON).
- [ ] A dedicated Admin page (`public/admin/audit_logs.php`) is implemented to view and filter logs.
- [ ] Only Admin users can access the Audit Log page.

## 5. Out of Scope
- Real-time audit log streaming.
- Automated alerting based on audit log patterns.
