# UAT Scenario: Role-Based Access Control

## Scenario ID: UAT-SEC-01
**Module:** Security & Access
**Role:** All Roles (to be tested sequentially)
**Priority:** High

## Description
Validate that permissions are correctly enforced across different user roles, ensuring users can only access and modify data appropriate for their function.

## Prerequisites
- Test user accounts exist for each role: Admin, Finance, Operational Staff, and Monitor.

## Test Steps
| Step | Role | Action | Expected Outcome |
|------|------|--------|------------------|
| 1    | Monitor | Log in and attempt to edit a Movement or Request. | Access is denied or "Edit" buttons are hidden. User can only view data. |
| 2    | Operational | Log in and attempt to record a payment or generate an invoice. | Access is restricted to operational tasks (Requests/Movements). Financial modules are restricted. |
| 3    | Finance | Log in and attempt to manage users or master data. | Access is restricted to financial tasks (Invoices/Payments/Reports). System management is restricted. |
| 4    | Admin | Log in and perform any action (edit, delete, manage users). | Full access is granted to all modules. |
| 5    | Any | Attempt to access an admin URL directly without logging in. | System redirects to Login page. |

## Expected Result
Permissions are strictly enforced based on the assigned role, preventing unauthorized access or modifications.

## Notes
- RBAC is a fundamental security requirement for the multi-user system.
- Ensure that the navigation menu dynamically hides/shows items based on role.
