# UAT Scenario: Payment Recording & Reconciliation

## Scenario ID: UAT-FIN-02
**Module:** Payment Tracking
**Role:** Finance
**Priority:** High

## Description
Validate the process of recording incoming payments (DP/FP) from agents, generating receipts, and reconciling with outgoing Payment Advises (to airlines).

## Prerequisites
- User is logged in as Finance.
- At least one active movement with a generated invoice exists.

## Test Steps
| Step | Action | Expected Outcome |
|------|--------|------------------|
| 1    | Navigate to "Record Payment". | Payment entry form is displayed. |
| 2    | Select an Invoice or Movement/Tour Code. | System identifies the pending amount and payment stage (DP1/DP2/FP). |
| 3    | Enter payment details (Date, Amount, Bank) and upload proof of payment. | Entry is successful. |
| 4    | Click "Save Payment". | Payment is recorded, and status is updated in Movement/Invoice. |
| 5    | Click "Generate Receipt". | A digital receipt (PDF) is generated for the agent. |
| 6    | Navigate to "Payment Report". | Verify the new payment appears in the rekap/report. |
| 7    | Navigate to "Payment Advise" list. | Create or view a Payment Advise for an airline to confirm top-up/payment. |
| 8    | Verify that Payment Report (incoming) and Payment Advise (outgoing) can be cross-referenced by Tour Code or PNR. | Reconciliation is possible. |

## Expected Result
Incoming payments are accurately recorded, receipts are generated, and the system provides clear reporting for financial reconciliation.

## Notes
- "Payment Advise" is used for airline top-ups/confirmations.
- Ensure the "Ticketing Done" status in Movement is linked to these financial clearances.
