# Specification: Align UAT Scenarios with Menu Definitions

## 1. Overview
This track aligns the existing User Acceptance Testing (UAT) scenarios with the confirmed business definitions:
- **External Invoice:** Corresponds to the "Invoice" menu.
- **Internal Invoice:** Corresponds to the "Payment Advice" menu.

The goal is to ensure the documentation accurately reflects the application structure and terminology.

## 2. Functional Requirements
### 2.1 Update Scenario 05 (Invoice Generation)
- **Refocus:** Rename or update the scenario to focus strictly on **External Invoice** generation via the "Invoice" menu.
- **Remove Ambiguity:** Remove any steps regarding a "Selection Prompt" between Internal/External versions within this specific menu.
- **Validation:** Focus on client-facing details, PDF layout compliance, and payment schedule (DP/FP).

### 2.2 Update Scenario 06 (Payment Recording & Reconciliation)
- **Incorporate Internal Perspective:** Enhance this scenario to serve as the validation for the **Internal Invoice** (Payment Advice) workflow.
- **Detail Addition:** Add specific steps for:
    - Verifying **Top Up Amount** and airline transfer details.
    - Validating **Bank Details** (Remitter EEMW vs. Recipient Airline).
    - Matching **Tour Code/PNR** for internal cost tracking.
    - Reviewing **Profit Margins** or internal markup notes if present.

### 2.3 Update UAT Master Plan
- **Terminology Alignment:** Update the "Test Scenarios Index" in `uat_master_plan.md` to explicitly use the "Internal (Payment Advice)" and "External (Invoice)" labels.

## 3. Non-Functional Requirements
- **Consistency:** Ensure all cross-references between scenarios remain valid.
- **Clarity:** Maintain non-technical language suitable for business owners (PIC Operational & PIC Finance).

## 4. Acceptance Criteria
- [ ] `05_invoice_generation.md` is updated to be "External Invoice" specific.
- [ ] `06_payment_recording_and_reconciliation.md` includes detailed "Internal Invoice" (Payment Advice) verification steps.
- [ ] `uat_master_plan.md` reflects the updated scenario names and menu mappings.
- [ ] Automated UAT readiness tests (e.g., `UatExportReadinessTest`) still pass.
