# Specification: Payment Report Alignment (TRID-HAJJ)

## 1. Overview
This track focuses on realigning the **Payment Report** module to strictly match the visual and functional requirements of the `TRID-HAJJ_1447H-2026M.pdf` reference. This includes a dual-table layout (Sales vs. Cost), enhanced field mapping for bank details and time limits, and a dynamic summary section for incentives and discounts.

## 2. Functional Requirements

### 2.1 Dual-Table Layout (Stacked)
- The Payment Report page will display two primary tables for a single Movement/PNR:
    - **Table 1: Sales / Internal (ELANG EMAS WISATA)** - Focused on customer fares and deposits.
    - **Table 2: Cost / Airline (e.g., FLYSCOOT)** - Focused on nett fares and payments to suppliers.
- Both tables will share the same header data (Agent, PNR, Tour Code, etc.).

### 2.2 Field Adjustments & Column Alignment
- **New Columns for both tables:**
    - `REFERENCE ID` (Existing)
    - `TIME LIMIT PAYMENT BY [ENTITY]` (Synced with Movement deadlines).
    - `DATE OF PAYMENT`
    - `TTL PAX`
    - `REMARKS`
    - `FARE PER-PAX`
    - `DEBET` (Amount)
- **Bank Information Columns:**
    - `FROM` (Bank Name), `BANK ACCOUNT NAME`, `BANK ACCOUNT NUMBER`.
    - `TO` (Bank Name), `BANK ACCOUNT NAME`, `BANK ACCOUNT NUMBER`.

### 2.3 Deadline Synchronization
- The `TIME LIMIT PAYMENT` field for deposit lines must automatically pull from the corresponding `movements` table fields:
    - Deposit 1 -> `deposit1_airlines_date` / `deposit1_eemw_date`
    - Deposit 2 -> `deposit2_airlines_date` / `deposit2_eemw_date`
    - Full Payment -> `fullpay_airlines_date` / `fullpay_eemw_date`
- Allow manual overrides if specific lines require different dates.

### 2.4 Summary & Balance Section
- Implement a footer summary that calculates:
    - **INCENTIVE:** Difference between Table 1 Balance and Table 2 Balance, or manual entry.
    - **DISCOUNT:** Manual entry field.
    - **FINAL BALANCE:** Calculated as `(Sales Balance - Cost Balance) - Discount`.

## 3. Database Changes
- Update `payment_report_lines` table:
    - Add `table_type` (ENUM: 'SALES', 'COST').
    - Add `time_limit_date` (DATE).
    - Add `bank_from_number` and `bank_to_number` (VARCHAR).
- Add `incentive_amount` and `discount_amount` to the `movements` table (or a new `payment_reports` header table).

## 4. Acceptance Criteria
- [ ] Payment Report UI displays two stacked tables (Sales and Cost).
- [ ] Columns match the reference PDF exactly (including bank details).
- [ ] "Time Limit Payment" automatically populates from Movement deadlines.
- [ ] The summary section correctly calculates the Incentive and Final Balance.
- [ ] All new fields are editable and persist to the database.

## 5. Out of Scope
- Integration with external bank APIs for real-time verification.
- Automated PDF export (will be handled in a separate "Export" track if needed).
