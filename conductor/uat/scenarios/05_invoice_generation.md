# UAT Scenario: External Invoice Generation

## Scenario ID: UAT-FIN-01
**Module:** Invoice Generator
**Role:** Finance / Admin
**Priority:** High

## Description
Validate the generation of proforma invoices for active movements via the "Invoice" menu, ensuring data integrity for the External (Client/Kyai) version.

## Prerequisites
- User is logged in as Finance or Admin.
- Active movements with PNR and Tour Code exist.

## Test Steps
| Step | Action | Expected Outcome |
|------|--------|------------------|
| 1    | Navigate to "Invoice" menu or a specific Movement detail. | Invoice generation options are available. |
| 2    | Click "Create Invoice". | System opens the invoice creation form pre-filled with movement data. |
| 3    | Verify client-facing fares, payment schedule (DP/FP), and bank details in the form. | Data is accurate and appropriate for the client. |
| 4    | Verify that fares and pax counts match the Movement data. | Data integrity is maintained. |
| 5    | Click "Save & Generate Invoice". | System saves the record and provides a preview/download option. |
| 6    | Download the PDF version of the invoice. | PDF is generated correctly and is readable. |
| 7    | Verify that the invoice follows the standard template contract (layout, logo, etc.). | Branding and layout are correct. |

## Expected Result
Professional PDF invoices are successfully generated for external use, accurately reflecting the financial data of the movement.

## Notes
- Refer to `invoice_template_contract.md` for layout requirements.
- External invoices must include clear instructions for DP-1, DP-2, and Fullpayment.
- This scenario focuses strictly on the "External" version (Invoice menu).