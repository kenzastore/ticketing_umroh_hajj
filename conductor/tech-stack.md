# Tech Stack: Digitalisasi Ticketing Umroh & Haji

## 1. Core Backend
- **Language:** Native PHP (8.x recommended)
- **Framework:** No framework (Native PHP approach)
- **Architecture:** Procedural or Simple OOP (to be determined by project structure)

## 2. Database
- **System:** MariaDB
- **Usage:** Relational storage for requests, bookings (PNR), payments, audit logs, and master data.

## 3. Frontend
- **Framework:** Bootstrap 5 (for responsive layouts and admin components)
- **Icons:** FontAwesome 6 (via CDN)
- **Interactivity:** Vanilla JavaScript (or minimal jQuery if required for legacy compatibility)
- **Design:** Mobile-first responsive grid system.

## 4. Document Generation
- **PDF Library:** dompdf (for generating Invoices, Reports, and Receipts from HTML templates)

## 5. Security & Infrastructure
- **Authentication:** Custom Session-based Auth
- **Authorization:** Role-Based Access Control (RBAC) (Admin, Finance, Monitor)
- **Security Features:** 
  - Input Sanitization & Prepared Statements (SQLi prevention)
  - Immutable Audit Logs
  - Encrypted/Hashed Receipt Verification
- **Deployment:** Standard LAMP/LEMP stack

## 6. Utilities
- **QR Code:** (Optional library like `phpqrcode` for receipts)
- **Exports:** Native CSV/Excel headers or `PhpSpreadsheet`

## 7. Testing
- **Framework:** PHPUnit (9.x)
- **Methodology:** Test-Driven Development (TDD)
- **Isolation:** Database transactional rollbacks per test case.
