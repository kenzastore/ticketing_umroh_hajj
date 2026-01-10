# Plan: TCP Group Split Validation

This plan outlines the implementation of a multi-layer validation system to ensure that the sum of passengers in split PNRs matches the Total Complete Party (TCP) for a group.

## Phase 1: Foundation & Backend Validation
Establish the data grouping logic and enforce strict rules at the database/API level.

- [x] Task: Database Schema Update - Ensure `Request ID` and `TCP` fields are correctly indexed and available in the `movements` table. b0d84fa
- [x] Task: Movement Model Enhancement - Add logic to `app/models/Movement.php` to calculate the current sum of passengers for a (Tour Code + Request ID) group. df69388
- [~] Task: API Validation - Implement server-side validation in the Movement controller to block saves if `SUM(Passenger) != TCP`.
- [ ] Task: Conductor - User Manual Verification 'Phase 1: Foundation & Backend Validation' (Protocol in workflow.md)

## Phase 2: Frontend Implementation
Enhance the user interface to provide real-time feedback and prevent invalid submissions.

- [ ] Task: Movement UI Update - Add a "Target TCP" input field and a "Current Sum" display to the Movement management screen.
- [ ] Task: Real-time UI Validation - Implement JavaScript logic to compare the live sum of split PNR passengers against the Target TCP.
- [ ] Task: UI Blocking - Disable the "Save" button and display a prominent error message when the TCP validation fails.
- [ ] Task: Conductor - User Manual Verification 'Phase 2: Frontend Implementation' (Protocol in workflow.md)

## Phase 3: Bulk Import Validation
Ensure that data integrity is maintained even when records are added via Excel/CSV.

- [ ] Task: Import Logic Update - Enhance the Movement import service to validate TCP sums for all groups within an uploaded file.
- [ ] Task: Bulk Error Reporting - Implement detailed error logging for the import process, identifying specifically which groups failed the TCP check.
- [ ] Task: Conductor - User Manual Verification 'Phase 3: Bulk Import Validation' (Protocol in workflow.md)
