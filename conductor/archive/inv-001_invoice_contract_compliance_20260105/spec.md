# Track Spec: INV-001 Invoice Contract Compliance

## Overview
This track ensures that the invoice generation process strictly adheres to the defined `invoice_template_contract.md`. The goal is to guarantee that the generated invoices (including their visual layout, data retrieval, field mapping, and export functionality) precisely match the specifications outlined in the contract, using the `ELANG_EMAS_WORKSHEET.xlsx` as the reference.

## Scope of Compliance
The compliance check will cover the following aspects of the invoice generation process:
- Visual Layout and Content of the generated invoice (HTML/PDF template).
- Data Retrieval process from the database that populates the invoice.
- Field Mapping between the contract's fields and the data source.
- Export Functionality (e.g., PDF generation) to ensure it correctly renders the compliant invoice.

## Verification Criteria

The verification will confirm strict adherence to the `invoice_template_contract.md` for each of the following invoice sections:

### 1. Header (cells `J4` to `J10`)
- Confirm exact cell positions, field names (`invoice_date`, `attention_to_name`, `recipient_company_name`, `recipient_address_line1`, `recipient_address_line2`), and data formats/types as specified.

### 2. Title + Reference (cells `C13`, `C14`)
- Confirm the exact fixed text "PROFORMA INVOICE" at `C13`.
- Confirm the exact format of `reference_id` as printed (e.g., `REF : <value>`) at `C14`.

### 3. Main Table Header (cells `C15` to `J15`)
- Confirm the exact header labels ("DOT", "DESCRIPTION", "TOTAL PAX", "FARES", "AMOUNT IN IDR") and their corresponding cells.

### 4. Summary Line (row `18`)
- Confirm the correct display of fields `dot_date` and `pnr`, including the "PNR : <value>" prefix, in row `18`.

### 5. Flight Information Section (default range `D20:G23`)
- Confirm adherence to the MVP rule of supporting up to 4 legs exactly as in the worksheet, including fields (`flight_date`, `flight_no`, `sector`, `time_range`) within the default row range.

### 6. Fare Breakdown + Payment Staging (rows `25-28`)
- Confirm correct labels and fields for the Base Fare row (row `25`).
- Confirm correct labels, percentages, and fields for the Staging rows (`26-28`) for Deposit-1, Deposit-2, and Fullpayment.
- Confirm the important behavior that staging values are commonly negative to represent deductions from the base total.

### 7. Totals (cells `I30`, `J30`, `I31`, `J31`)
- Confirm that the values in cells `J30` (`total_payment_idr`) and `J31` (`balance_idr`) are correctly calculated and displayed.

### 8. Remarks (cells `C32` to `C37`)
- Confirm the presence of the "REMARKS" label at `C32` and the availability of 5 lines for remarks (`C33` to `C37`).
- Confirm that remarks adhere to the rule of maintaining consistent numbering and sentence order to match the worksheet.

### 9. Time Limit (cells `C39` to `E42`)
- Confirm the header "6. TIME LIMIT" at `C38`, the labels in column C (e.g., "Deposit-1", "Deposit-2", "Fullpayment", "Ticketing").
- Confirm that corresponding deadline dates are correctly mapped to fields like `deposit_1_deadline`, `deposit_2_deadline`, `fullpayment_deadline`, and `ticketing_deadline` in column E.

### 10. Bank Transfer Details (cells `C46` to `C48`)
- Confirm the label "Payment via bank transfer to Bank account:" at `C44` and the presence of fields for Bank/Branch, Account Name, and Account No.
- Confirm that these map correctly to fields like `bank_name`, `bank_account_name`, and `bank_account_no`.

### 11. Footer Contact (cells `C52` to `C54`)
- Confirm the presence of fields for `issuer_company_name`, `contact_person_name`, and `contact_phone` in cells `C52` to `C54`.
- Confirm that these fields correctly capture the relevant contact information.

## Acceptance Criteria
- The invoice generation process strictly adheres to all specified layout, field, data mapping, and export requirements detailed in the `invoice_template_contract.md`.
- PDF exports of invoices are generated accurately and match the contract specifications.
