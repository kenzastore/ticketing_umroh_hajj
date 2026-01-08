# UAT Scenario: Payment Recording & Internal Invoice (Payment Advice)

## Scenario ID: UAT-FIN-02
**Module:** Payment Tracking
**Role:** Finance
**Priority:** High

## Description
Validate the process of recording incoming payments (DP/FP) from agents, generating receipts, and generating the **Internal Invoice (Payment Advice)** for airline transfers and internal cost tracking.

## Prerequisites
- User is logged in as Finance.
- At least one active movement with a generated invoice exists.

## Test Steps
| Step | Action | Expected Outcome |
|------|--------|------------------|
| 1    | Navigate to "Record Payment". | Payment entry form is displayed. |
| 2    | Select an Invoice or Movement/Tour Code. | System identifies the pending amount and payment stage (DP1/DP2/FP). |
| 3    | Enter payment details (Date, Amount, Bank) and upload proof of payment. | Entry is successful. |
| 4    | Click "Save Payment" and then "Generate Receipt". | Payment is recorded, and a digital receipt (PDF) is generated for the agent. |
| 5    | Navigate to "Payment Advice" (Internal Invoice) menu. | List of payment advises is displayed. |
| 6    | Click "Create New Advice" and select the Movement/PNR. | System pre-fills internal cost data (Approved Fare, Seats). |
| 7    | Verify **Top Up Amount** and airline transfer details (20%/80% logic). | Calculations match internal cost requirements. |
| 8    | Validate **Bank Details (Remitter vs Recipient)** for the airline transfer. | Remitter (EEMW) and Recipient (Airline) details are accurate. |
| 9    | Verify that **Tour Code/PNR** matches the Movement for internal tracking. | Internal cost tracking is correctly linked. |
| 10   | (If applicable) Review **Profit Margins** or internal markup notes. | Internal financial visibility is confirmed. |
| 11   | Save and Verify that the new payment appears in the "Payment Report". | Reconciliation between incoming and outgoing flows is possible. |

## Expected Result
Incoming payments are accurately recorded, receipts are generated, and the **Internal Invoice (Payment Advice)** is successfully created with correct airline transfer and cost details.

## Notes
- "Payment Advice" is the definitive "Internal Invoice" for tracking company costs and airline obligations.
- Ensure the "Ticketing Done" status in Movement is linked to these financial clearances.