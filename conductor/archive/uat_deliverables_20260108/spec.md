# Specification: UAT Deliverables for End-to-End Workflow Validation

## 1. Overview
This track focuses on creating the definitive User Acceptance Testing (UAT) framework for the Ticketing Umroh & Haji system. It aims to provide business users with a structured method to validate that the software meets all functional requirements and is ready for production.

## 2. Functional Requirements
### 2.1 Test Scenario Development
Create detailed, step-by-step test scenarios for the following core processes:
- **Request to Movement:** Entry of multi-segment booking requests and their conversion into active movements.
- **Financial Lifecycle:** Proforma invoice generation, payment recording (DP/FP), and reconciliation with Payment Reports and Advises.
- **Operational Dashboards:** Validation of the "FullView" Movement dashboard, filtering logic, and the H-3 reminder system.
- **Access Control:** Verification that permissions are correctly enforced for Admin, Finance, Operational, and Monitor roles.

### 2.2 Documentation Templates
Design Markdown-based templates for:
- **UAT Test Scenarios:** Clear instructions, expected results, and role assignments.
- **UAT Execution Logs:** Tables for recording Pass/Fail status, defect details, user feedback, and sign-offs.

## 3. Non-Functional Requirements
- **Format:** All deliverables must be in Markdown for version control, with a clear structure suitable for PDF export.
- **Clarity:** Scenarios must be written in non-technical language appropriate for business stakeholders.

## 4. Acceptance Criteria
- [ ] Scenarios cover 100% of the core modules (Request, Movement, Invoice, Payment, Notification).
- [ ] Templates include fields for Defect ID, Severity, and User Comments.
- [ ] Documentation is organized within `conductor/tracks/uat_deliverables_20260108/uat/` or a similar structured path.

## 5. Out of Scope
- Automated UI testing (e.g., Selenium/Cypress) is not part of this track.
- Performance or stress testing deliverables.
