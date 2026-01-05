# Track Spec: PDF Generation Service

## Overview
Implement a service to generate PDF documents for Invoices and Receipts using the `dompdf` library. This allows the system to provide professional, downloadable documents derived from the database.

## Objectives
1. **Integrate dompdf:** Install or include the library.
2. **Invoice Templates:** Create HTML/CSS templates for Internal and External invoices.
3. **Receipt Templates:** Create templates for Encrypted Digital Receipts.
4. **PDF Engine:** Develop a helper class/function to render HTML to PDF and serve it or save it to `public/assets/docs/`.

## Requirements
- Support UTF-8 (for special characters).
- Professional layout matching the worksheet style.
- Secure token validation for downloading (linked to `documents` table).

## Acceptance Criteria
- [ ] dompdf is integrated and functional.
- [ ] Invoices can be downloaded as PDF with correct data.
- [ ] Receipts include a verification QR code/link.
