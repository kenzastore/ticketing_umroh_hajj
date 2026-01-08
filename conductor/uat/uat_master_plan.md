# UAT Master Plan: Ticketing Umroh & Haji

## Introduction
This document outlines the User Acceptance Testing (UAT) plan for the Digital Ticketing system. The goal is to ensure the system is ready for production use by validating it against real-world business processes.

## Objectives
- Confirm all functional requirements are met.
- Validate end-to-end business workflows.
- Ensure data integrity and security permissions.
- Obtain formal sign-off from business stakeholders.

## Execution Instructions
1. **Preparation:** Ensure the UAT environment is seeded with test data.
2. **Roles:** Assign testers to specific roles (Admin, Finance, Operational, Monitor).
3. **Execution:** Testers follow the step-by-step instructions in each scenario.
4. **Recording:** Results, defects, and feedback must be recorded in the provided result templates.
5. **Sign-off:** Upon successful completion, obtain signatures on the result documents.

## Test Scenarios Index
| ID | Module | Scenario Description | Link |
|----|--------|----------------------|------|
| UAT-REQ-01 | Request Management | [New Booking Request](./scenarios/01_new_booking_request.md) |
| UAT-MOV-01 | Movement Monitoring | [Request to Movement Conversion](./scenarios/02_request_to_movement_conversion.md) |
| UAT-MOV-02 | Movement Monitoring | [Movement Dashboard & FullView](./scenarios/03_movement_dashboard_and_fullview.md) |
| UAT-NOT-01 | Notification System | [Time Limit Reminders](./scenarios/04_time_limit_reminders.md) |
| UAT-FIN-01 | Invoice Generator | [Invoice Generation](./scenarios/05_invoice_generation.md) |
| UAT-FIN-02 | Payment Tracking | [Payment Recording & Reconciliation](./scenarios/06_payment_recording_and_reconciliation.md) |
| UAT-SEC-01 | Security & Access | [Role-Based Access Control](./scenarios/07_role_based_access_control.md) |
| UAT-SEC-02 | Security & Audit | [Audit Logging](./scenarios/08_audit_logging.md) |

## Defect Management
- **Critical:** Prevents a core business process. Must be fixed before release.
- **Major:** Significant functional issue with a workaround. Should be fixed.
- **Minor:** UI/UX or non-critical issue. Can be deferred.

## Acceptance Criteria
- 100% of Critical scenarios must pass.
- No open "Critical" defects.
- Formal sign-off obtained from PIC Operational and PIC Keuangan.
