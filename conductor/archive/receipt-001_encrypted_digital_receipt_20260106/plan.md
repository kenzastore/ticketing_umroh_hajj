# Track Plan: RECEIPT-001 Encrypted Digital Receipt

## Tasks

### Phase 1: Verification Logic
- [x] **Task 1: Implement Verification Page**
    -   **File:** `public/verify_receipt.php`
    -   **Action:** Create a page that takes a `token` GET parameter and queries the `payments` table to verify authenticity.

### Phase 2: Receipt Generation
- [x] **Task 2: Create PDF Receipt Generator**
    -   **File:** `public/finance/print_receipt.php`
    -   **Action:** Create a script that uses `dompdf` and `receipt_template.php` to output a PDF.
    -   **QR Code:** Use an external API (like Google Charts) or embedded generator for the QR.

### Phase 3: Integration
- [x] **Task 3: Add Download Receipt Buttons**
    -   **File:** `public/finance/invoice_detail.php`
    -   **Action:** Add a "Receipt" icon/button for every payment listed in the history.

- [x] **Task 4: Final Verification**
    -   **Action:** Record a payment, download the receipt, scan the QR (manually test link), and verify the token.
