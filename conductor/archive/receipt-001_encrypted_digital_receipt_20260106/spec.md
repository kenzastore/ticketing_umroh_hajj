# Track Spec: RECEIPT-001 Encrypted Digital Receipt

## Overview
This track implements a secure, anti-tamper digital receipt system. Every payment recorded generates a unique verification token and a QR code. Stakeholders can verify the authenticity of a receipt by scanning the QR code, which leads to a public validation page.

## Functional Requirements

### 1. Receipt Generation
- **Trigger:** When a payment is recorded in the Finance module.
- **Content:**
    - Amount Paid
    - Payment Date
    - PNR / Tour Code
    - Received From (Agent/Corporate)
    - Unique Receipt Token (Hash)
    - QR Code for verification.

### 2. Anti-Tamper Verification
- **Token:** A 64-character SHA-256 hash stored in the `payments` table (`receipt_hash`).
- **QR Code:** Points to a URL: `https://<domain>/verify_receipt.php?token=<hash>`.
- **Validation Page:** A publicly accessible page that:
    - Confirms if the token exists.
    - Displays the payment details (Amount, Date, PNR) if valid.
    - Shows a "TAMPERED OR INVALID" warning if not found.

### 3. PDF Output
- Use `dompdf` to generate a downloadable PDF version of the receipt.

## Technical Requirements
- **Model Update:** Ensure `Payment::create` generates a secure unique hash (Done in previous tasks, but verify).
- **Template:** Enhance `app/templates/receipt_template.php`.
- **Pages:**
    - `public/finance/print_receipt.php` (Generates PDF)
    - `public/verify_receipt.php` (Public validation)

## Acceptance Criteria
- [ ] Recording a payment generates a unique `receipt_hash`.
- [ ] A "Download Receipt" button is available on the invoice detail page for each payment.
- [ ] The PDF receipt contains a working QR code pointing to the verification page.
- [ ] The verification page correctly identifies valid and invalid tokens.
