# UAT Scenario: Invoice Generation

## Scenario ID: UAT-FIN-01
**Module:** Invoice Generator
**Role:** Finance / Admin
**Priority:** High

## Description
Validate the generation of proforma invoices for active movements, ensuring data integrity for both Internal and External (Client/Kyai) versions.

## Prerequisites
- User is logged in as Finance or Admin.
- Active movements with PNR and Tour Code exist.

## Test Steps
| Step | Action | Expected Outcome |
|------|--------|------------------|
| 1    | Navigate to "Invoices" or a specific Movement detail. | Invoice generation options are available. |
| 2    | Click "Generate Invoice". | System prompts for invoice type (Internal/External). |
| 3    | Select "Internal Invoice" and preview. | PDF/Preview shows internal costs, fares, and agent-specific data. |
| 4    | Select "External Invoice" and preview. | PDF/Preview shows client-facing fares, payment schedule (DP/FP), and bank details. |
| 5    | Verify that fares and pax counts match the Movement data. | Data integrity is maintained. |
| 6    | Download the PDF version of the invoice. | PDF is generated correctly and is readable. |
| 7    | Verify that the invoice follows the standard template contract (layout, logo, etc.). | Branding and layout are correct. |

## Expected Result
Professional PDF invoices are successfully generated for both internal and external use, accurately reflecting the financial data of the movement.

## Notes
- Refer to `invoice_template_contract.md` for layout requirements.
- External invoices must include clear instructions for DP-1, DP-2, and Fullpayment.
