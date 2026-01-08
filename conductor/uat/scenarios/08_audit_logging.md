# UAT Scenario: Audit Logging

## Scenario ID: UAT-SEC-02
**Module:** Security & Audit
**Role:** Admin
**Priority:** Medium

## Description
Validate that all critical actions (create, update, delete, login) are recorded in the system's audit log for tracking and accountability.

## Prerequisites
- User is logged in as Admin.
- Some actions have been performed recently (e.g., creating a request, updating a movement).

## Test Steps
| Step | Action | Expected Outcome |
|------|--------|------------------|
| 1    | Perform a critical action: Create a new Booking Request. | Action is successful. |
| 2    | Perform another action: Update an existing Movement's PNR. | Action is successful. |
| 3    | Navigate to "Audit Logs" (Admin only). | Audit log list is displayed. |
| 4    | Search for your username and today's date. | Recent actions are listed. |
| 5    | Verify the "Action" column reflects "Create Request" and "Update Movement". | Actions are accurately described. |
| 6    | Verify the "Timestamp" and "User" columns are correct. | Metadata is accurate. |
| 7    | (If applicable) Verify the "Old Value" and "New Value" are recorded for updates. | Change details are visible. |

## Expected Result
All critical system actions are transparently recorded in the audit log, providing a reliable trail for administrative review.

## Notes
- Audit logs are immutable and should not be editable by any user.
- Ensure that sensitive data (like passwords) are NEVER recorded in the audit logs.
