# Track Plan: PDF Generation Service

This plan outlines the steps to implement PDF document generation.

## Phase 1: Setup & Engine
1. **Include dompdf:**
   - Download or reference dompdf (assuming manual inclusion for native PHP project).
   - Create `includes/pdf_engine.php` wrapper.
2. **Setup Storage:**
   - Ensure `public/assets/docs/` exists and is writable.

## Phase 2: Invoice PDF
1. **HTML Template:** Create `app/templates/invoice_template.php`.
2. **Controller Integration:** Update `public/finance/print_invoice.php` to use the engine.
3. **Verification:** Test with existing invoices.

## Phase 3: Receipt PDF
1. **HTML Template:** Create `app/templates/receipt_template.php`.
2. **Logic:** Implement unique token/QR generation for the receipt.

## Steps
- [x] Integrate `dompdf` library (via `composer.json`).
- [x] Create `includes/pdf_engine.php`.
- [x] Implement `app/templates/invoice_template.php`.
- [x] Refactor `public/finance/print_invoice.php` to generate PDF.
- [x] Implement Receipt PDF generation.
