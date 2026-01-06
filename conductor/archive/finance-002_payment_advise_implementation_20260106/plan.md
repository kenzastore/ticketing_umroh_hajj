# Track Plan: FINANCE-002 Payment Advise Implementation

## Tasks

### Phase 1: Foundation
- [x] **Task 1: Database Migration**
    -   **Action:** Create and run `database/create_payment_advises_table.sql`.
    -   **Fields:** All fields identified in spec.md.

- [x] **Task 2: Model Implementation**
    -   **File:** `app/models/PaymentAdvise.php`
    -   **Action:** Implement `create`, `readAll`, `readById` with appropriate data mapping.

### Phase 2: User Interface
- [x] **Task 3: Create Advise Generation Form**
    -   **File:** `public/finance/create_payment_advise.php`
    -   **Action:** Build form to select Movement and input top-up/bank details.

- [x] **Task 4: Implement Detail View (PDF Style)**
    -   **File:** `public/finance/payment_advise_detail.php`
    -   **Action:** Design the page to match `PAYMENT_ADVISE.pdf`.

- [x] **Task 5: List View**
    -   **File:** `public/finance/payment_advise_list.php`
    -   **Action:** Create a searchable table of all advises.

### Phase 3: Integration
- [x] **Task 6: Dashboard & Menu Links**
    -   **File:** `public/shared/header.php` and `public/finance/dashboard.php`.
    -   **Action:** Add "Payment Advise" to the navigation.

- [x] **Task 7: Final Verification**
    -   **Action:** End-to-end test from Movement selection to Advise printing.
