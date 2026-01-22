# Plan: Payment Report Alignment (TRID-HAJJ)

## Phase 1: Database & Model Enhancement
Establish the data structure to support dual tables, bank details, and summary fields.

- [x] Task: Database Schema Update - Add `table_type`, `time_limit_date`, and bank account numbers to `payment_report_lines`. Add `incentive_amount` and `discount_amount` to `movements`. [28721]
- [x] Task: Model Enhancement - Update `app/models/PaymentReport.php` to fetch and separate lines into `SALES` and `COST` arrays. [a0ae4ba]
- [x] Task: Write Unit Tests - Create `tests/PaymentReportModelTest.php` to verify data separation and summary calculations. [pass]
- [x] Task: Conductor - User Manual Verification 'Phase 1: Database & Model Enhancement' (Protocol in workflow.md) [pass]

## Phase 2: UI Redesign (Dual Tables)
Reconstruct the Payment Report interface to match the reference PDF's stacked layout.

- [x] Task: UI Refactoring - Update `public/finance/payment_report.php` to render two distinct tables (Internal/Sales and Airline/Cost). [pass]
- [x] Task: Column Alignment - Update table headers and data rows to include Bank Account Numbers and Time Limit Payment columns. [pass]
- [x] Task: Summary Implementation - Implement the footer section for Incentive, Discount, and Final Balance calculations. [pass]
- [x] Task: Write Integration Tests - Verify the visual structure and data mapping in `tests/PaymentReportUiTest.php`. [pass]
- [x] Task: Conductor - User Manual Verification 'Phase 2: UI Redesign (Dual Tables)' (Protocol in workflow.md) [pass]

## Phase 3: Data Persistence & Synchronization
Implement the editing capabilities and ensure deadlines stay in sync with the Movement module.

- [x] Task: Edit Functionality - Create/Update logic to allow Finance users to modify payment lines, bank details, and summary values. [pass]
- [x] Task: Deadline Sync Logic - Implement automatic fetching of "Time Limit" dates from `movements` (DP1/DP2/FP) when specific remarks are detected (e.g., "DEPOSIT"). [pass]
- [x] Task: Audit Logging - Ensure all changes to payment report lines are captured in the system's audit logs. [pass]
- [x] Task: Final End-to-End Verification - Perform a complete walkthrough from data entry to report viewing. [pass]
- [x] Task: Conductor - User Manual Verification 'Phase 3: Data Persistence & Synchronization' (Protocol in workflow.md) [pass]
