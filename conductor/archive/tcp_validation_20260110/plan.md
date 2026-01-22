# Plan: TCP Group Split Validation

This plan outlines the implementation of a multi-layer validation system to ensure that the sum of passengers in split PNRs matches the Total Complete Party (TCP) for a group.

## Phase 1: Foundation & Backend Validation
Establish the data grouping logic and enforce strict rules at the database/API level.

- [x] Task: Database Schema Update - Ensure `Request ID` and `TCP` fields are correctly indexed and available in the `movements` table. b0d84fa
- [x] Task: Movement Model Enhancement - Add logic to `app/models/Movement.php` to calculate the current sum of passengers for a (Tour Code + Request ID) group. df69388
- [x] Task: API Validation - Implement server-side validation in the Movement controller to block saves if `SUM(Passenger) != TCP`. df69388
- [ ] Task: Conductor - User Manual Verification 'Phase 1: Foundation & Backend Validation' (Protocol in workflow.md)

## Phase 2: Frontend Implementation
Enhance the user interface to provide real-time feedback and prevent invalid submissions.

- [x] Task: Movement UI Update - Add a "Target TCP" input field and a "Current Sum" display to the Movement management screen. e265381
- [x] Task: Real-time UI Validation - Implement JavaScript logic to compare the live sum of split PNR passengers against the Target TCP. e265381
- [x] Task: UI Blocking - Disable the "Save" button and display a prominent error message when the TCP validation fails. e265381
- [x] Task: Conductor - User Manual Verification 'Phase 2: Frontend Implementation' (Protocol in workflow.md) [checkpoint: e265381]

## Phase 3: Bulk Import Validation
**Note:** No existing Movement Import service was found in the codebase. The strict validation implemented in `Movement::create` (Phase 1) ensures that any future import feature using the model will be automatically protected.

- [x] Task: Import Logic Update - Covered by Model Validation in Phase 1.
- [x] Task: Bulk Error Reporting - Covered by Model Validation in Phase 1.
- [x] Task: Conductor - User Manual Verification 'Phase 3: Bulk Import Validation' (Protocol in workflow.md) [checkpoint: e265381]
