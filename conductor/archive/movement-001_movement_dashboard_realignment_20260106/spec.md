# Track Spec: MOVEMENT-001 Movement Dashboard Realignment

## Overview
This track realigns the Movement Dashboard (`public/admin/movement_fullview.php`) to match the detailed, wide-format layout provided in `movement.pdf`. This includes separating data into "UMRAH MOVEMENT" and "HAJJI MOVEMENT" sections, and adding numerous columns for financial tracking and flight details.

## Functional Requirements

### 1. Data Structure
- **Categorization:** Separate `UMRAH` and `HAJJI` movements.
- **Fields:** The dashboard must display (columns from left to right as per PDF):
    - NO, Travel Agent, Creation, PNR, DP1, DP2, FP, TOIR CODE, CARRIER
    - Flight No, Sector, DEP.SEG-1, DEP.SEG-2, ARR. SEG-3, ARR-SEG4
    - Sector, Flight No, PATTERN, Passenger
    - Approved Fare, Selling, NETT SELLING, TOTAL SELLING
    - 1ST DEPOSIT AIRLINES, DATE OF 1ST DEPOSIT AIRLINES, DATE OF 1ST DEPOSIT EEMW
    - 2ND DEPOSIT AIRLINES, DATE OF 2ND DEPOSIT AIRLINES, DATE OF 2ND DEPOSIT EEMW
    - DATE OF FULLPAY TO AIRLINES, DATE OF FULLPAY TO EEMW
    - TIME LIMIT MANIFEST & TICKETING (16 DAYS EARLIER)
    - AMOUNT NETT BALANCE PAY, AMOUNT SELL BALANCE PAY
    - TICKETING DONE (Checkbox/Icon)
    - BELONGING TO (Dropdown/Text)
    - DURATION, ADD-1 DAYS, TTL DAYS

### 2. UI Layout
- **Style:** Wide table with horizontal scrolling (`.table-responsive`).
- **Headers:** Two-row header (if needed) or grouped headers similar to Booking Request.
- **Color Coding:**
    -   Orange/Brown for Payment Headers (DP, FP).
    -   Yellow highlights for specific date columns or statuses.
    -   Green highlights for positive financial/status indicators (e.g., "PAID").
    -   Red for warnings or "UNPAID".

### 3. Logic
- **API:** `public/api/movement.php` must return all these fields.
- **Calculations:** Some columns like `TOTAL SELLING` (Selling * Passenger) or Balance Pay might be calculated on the fly or stored.
- **Grouping:** Display two separate tables or sections: one for Umrah, one for Hajji.

## Technical Requirements
- **Database:**
    -   Add `category` ENUM('UMRAH', 'HAJJI') to `movements` table.
    -   Ensure all financial columns (deposits, dates) exist in `movements` schema (already largely present, verify names).
- **Frontend:**
    -   Update `public/admin/movement_fullview.php`.
    -   Use `fetch` to get data and render two distinct table bodies.

## Acceptance Criteria
- [ ] Database schema updated with `category` column.
- [ ] API returns data correctly grouped or filterable by category.
- [ ] Dashboard displays two sections: Umrah and Hajji.
- [ ] All columns from the PDF are present and populated.
- [ ] Horizontal scrolling works smoothly.
- [ ] Financial columns display formatted currency values.
