# Track Plan: FINANCE-001 Payment Report Implementation

## Tasks

### Phase 1: Model & Logic
- [x] **Task 1: Create PaymentReport Model**
    -   **File:** `app/models/PaymentReport.php`
    -   **Action:** Implement methods to fetch consolidated data for a movement ID: `getReportByMovementId($movementId)`.

### Phase 2: UI Implementation
- [x] **Task 2: Create Payment Report Page**
    -   **File:** `public/finance/payment_report.php`
    -   **Action:** Implement the layout based on `PAYMENT_REPORT.pdf`.
    -   **Tech:** PHP, Bootstrap 5.

- [x] **Task 3: Implement Calculation Logic**
    -   **Action:** Ensure Nett vs Selling calculations, Incentives, and Discounts are handled correctly in the view/model.

### Phase 3: Integration & Polish
- [x] **Task 4: Add Link from Dashboard**
    -   **File:** `public/finance/dashboard.php` and `public/admin/dashboard.php`
    -   **Action:** Link to the new report page.

- [x] **Task 5: Export Functionality (Optional but recommended)**
    -   **Action:** Add PDF/Excel export for the report.
