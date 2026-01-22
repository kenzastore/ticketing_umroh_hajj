# Specification: TCP Group Split Validation

## Overview
Implement a strict validation mechanism to ensure data integrity when a single group is split into multiple PNRs (Passenger Name Records). The core rule is that the sum of passengers allocated to each PNR must exactly match the manually defined Total Complete Party (TCP) for that group.

## Functional Requirements

### 1. Data Grouping Logic
- A "Group" is uniquely identified by the combination of:
  - `TOUR CODE`
  - `Request ID / Ref ID`
- Validation must consider all records sharing both these identifiers.

### 2. TCP Establishment
- The **Target TCP** (Total Group Size) will be manually entered by the user.
- This value serves as the "source of truth" for the validation.

### 3. Multi-Layer Validation
- **Frontend (UI):** Implement real-time calculation in the Movement/PNR input forms. Provide immediate visual feedback if the current sum of passengers does not match the Target TCP.
- **Backend (API):** Enforce validation on the server side during all create/update operations for Movement records.
- **Bulk Import:** Validate all records during Excel/CSV uploads. If a group within the import file (or combined with existing records) fails the TCP sum check, the import for that group must be rejected.

### 4. Enforcement Behavior
- **Strict Blocking:** The system must prevent saving or updating records if `SUM(Passenger counts) != Target TCP`. 
- Users must correct the passenger allocation or update the Target TCP before the data is committed to the database.

## Acceptance Criteria
- [ ] UI displays an error message and disables the "Save" button if the passenger sum is incorrect.
- [ ] API returns a 422 Unprocessable Entity (or similar error) if a save request violates the TCP rule.
- [ ] Bulk import logs specific errors for groups with mismatched TCP sums and prevents their ingestion.
- [ ] Validation correctly handles groups using the dual-identifier (Tour Code + Request ID).
