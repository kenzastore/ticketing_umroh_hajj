# Specification: Update User Manual for Split PNR and TCP Validation

## Overview
Update the project's user manual documentation to reflect recent system changes regarding Split PNR handling and the mandatory TCP (Total Complete Party) validation rules. The goal is to ensure operational staff understand how to allocate passengers across multiple PNRs while maintaining group data integrity.

## Proposed Changes

### 1. Stage 1: Demand Intake (`docs/manual/stage1_demand_intake.md`)
- Clarify the role of the "Group Size" and "TCP" fields during the initial request phase.
- Ensure "Verifikasi 1.3" correctly references the TCP concept as the "total intended group size".

### 2. Stage 2: Operational Execution (`docs/manual/stage2_operational_execution.md`)
- Add a new section: **"4. Handling Split PNRs"**.
- Detail the step-by-step process:
    - Entering the **Target TCP** for the group.
    - Creating multiple Movement records (splits) sharing the same **Tour Code** and **Movement No (Request ID)**.
    - Monitoring the **Current Group Sum** indicator.
- Explain the **Strict Validation Rule**:
    - The system blocks saving if `SUM(Passenger counts) > Target TCP`.
    - Visual cues: Sum turns red, and the "Update" button changes to a disabled "TCP Exceeded" state.

### 3. Terminology/Glossary (`docs/manual/index.md`)
- Add a "Terminology" section at the bottom of the index page.
- Define **TCP (Total Complete Party)**: The total number of passengers in the entire group, regardless of PNR splits.
- Define **Passenger Count (Group Size per PNR)**: The number of passengers allocated to a specific PNR within a split.

## Acceptance Criteria
- [ ] `stage1_demand_intake.md` correctly mentions the TCP concept.
- [ ] `stage2_operational_execution.md` includes the "Handling Split PNRs" section with validation details.
- [ ] `index.md` contains a clear Glossary/Terminology section defining TCP and Passenger Count.
- [ ] Documentation accurately reflects the behavior of the `edit_movement.php` interface.
