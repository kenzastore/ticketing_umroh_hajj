# Track Spec: FINANCE-001 Payment Report Implementation

## Overview
This track implements a detailed, group-based Payment Report page that matches the `PAYMENT_REPORT.pdf` reference. The report aggregates flight information, payment transactions, and financial summaries (Selling vs Nett fares, Incentives, Discounts) for a specific movement/group.

## Functional Requirements

### 1. Report Header
- **Fields:**
    - UPDATED (Date)
    - NAMA AGENT
    - DATE OF REQUEST
    - DATE OF CONFIMED
    - PROGRAM (e.g. 18DAYS)
    - PNR
    - TOUR CODE
    - AUTHORIZED (User/Admin)
    - REF ID

### 2. Flight Details Section
- **Table Columns:**
    - NO
    - DATE DEP/ARR
    - FLIGHT NO
    - CITY
    - TIME

### 3. Payment/Accounting Section
- **Table Columns:**
    - REFERENCE ID
    - DATE OF PAYMENT
    - TTL PAX
    - REMARKS
    - FARE PER-PAX
    - DEBET
- **Sub-sections:**
    - SELLING FARES
    - NETT FARES
    - BALANCE calculation for both.

### 4. Bank Information Section
- **Table Columns:**
    - FROM
    - BANK ACCOUNT NAME (From)
    - TO
    - BANK ACCOUNT NAME (To)

### 5. Final Summary
- **Fields:**
    - INCENTIVE
    - DISCOUNT
    - FINAL BALANCE

## Technical Requirements
- **Model:** Implement `app/models/PaymentReport.php`.
- **UI:** Update/Create `public/finance/payment_report.php` using Bootstrap 5.
- **Data Integration:** 
    - Fetch movement data for headers and flight segments.
    - Fetch payment/invoice data for the accounting section.
    - Map `payment_report_lines` table if used, or aggregate from `payments` and `movements`.

## Acceptance Criteria
- [ ] The report layout matches the PDF reference structure.
- [ ] Header information is correctly populated from the movement/group data.
- [ ] Flight segments are displayed correctly.
- [ ] Payment transactions (Fares, Deposits, Fullpays) are listed with correct totals.
- [ ] Nett vs Selling fare logic is implemented for balance comparison.
- [ ] Bank details are correctly displayed for each payment.
- [ ] Final balance and incentives are calculated and shown.
