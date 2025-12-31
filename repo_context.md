# repo_context.md — Digitalisasi Ticketing Umroh & Haji

This file complements **AGENTS_Ticketing_Umroh_Haji.md** and provides **repo-specific** context:
how to run the app, where things live, key conventions, and the minimum business rules that must not be broken.

> Keep this document short, accurate, and updated. If something changes in the repo, update this file first.

---

## 1) Project Snapshot

- **Product**: Digitalisasi Proses Ticketing Umroh & Haji (Web-Based + FullView Display)
- **Primary Users**: Ticketing Admin, Finance, Viewer/Monitor
- **Core Flow**: Request → Seat/PNR → Movement → Invoice → Payment/FP Merah → Report → Encrypted Receipt
- **Tech Stack**: Native PHP (PDO) + MariaDB

---

## 2) Tech Stack & Runtime Requirements

### Backend
- PHP **8.x**
- MariaDB **10.x**
- Web server: Apache or Nginx
- PDF: Dompdf
- QR/token: QR library + HMAC signing

### Frontend
- Bootstrap (responsive FullView Display)
- Vanilla JS (filters, quick actions, modals)

### Recommended Local Dev (Windows)
- Laragon (Apache/Nginx + PHP + MariaDB)

---

## 3) How to Run Locally

### A) Configure Database
1. Create database: `ticketing_umroh`
2. Import schema: `database/schema.sql` (or run migrations if available)
3. Create a user and grant privileges.

### B) Configure App
- Copy config template:
  - `config/config.example.php` → `config/config.php`
- Set required values (DB and secrets).

### C) Run
- If using Laragon: point project folder to Laragon www directory and run via `http://localhost/<project>`
- If using PHP built-in server (optional):
```bash
php -S localhost:8000 -t public
```

---

## 4) Environment / Config Keys

Store secrets in `config/config.php` (or environment variables if supported).

Minimum keys:
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
- `APP_BASE_URL`
- `APP_ENV` (local/staging/prod)
- `HMAC_SECRET` (for receipt token signing)
- `UPLOAD_DIR` (outside webroot if possible)
- `MAX_UPLOAD_MB`

---

## 5) Repository Structure (Recommended)

> Adjust this section to match the real repo tree once created.

```
/public
  index.php
  assets/
    css/
    js/
    img/
/app
  controllers/
  services/
  repositories/
  views/
  helpers/
/config
  config.php
/database
  schema.sql
  seeds/
/storage
  uploads/
    payment_proofs/
    airline_confirmations/
    generated_pdfs/
  logs/
/vendor
  (composer dependencies)
```

---

## 6) Key Business Rules (Do Not Break)

### Status Model
Use these statuses consistently:
- `NEW`, `QUOTED`, `BLOCKED`, `PNR_ISSUED`, `IN_MOVEMENT`, `INVOICED`,
  `PAID_DEPOSIT`, `PAID_FULL`, `CONFIRMED_FP_MERAH`, `REPORTED`, `RECEIPTED`,
  `CHANGED`, `CANCELED`

### FP Merah Validation
`CONFIRMED_FP_MERAH` can only be set when:
1) Payment proof exists AND
2) Airline confirmation exists AND
3) Finance user validates the record

### Change Events
Extra flight / reschedule / cancel must:
- Create a change event (actor, timestamp, reason)
- Update movement
- Trigger finance impact review (delta/refund/penalty)
- Re-issue docs (invoice/report/receipt) if needed

---

## 7) Data Model (Minimal Tables)

> Keep this aligned with actual schema.

- `users`, `roles`, `permissions`
- `agents`, `requesters`
- `requests` (group/individual), `bookings` (PNR)
- `movements` (daily/group movement)
- `invoices` (internal/kyai), `invoice_items` (optional)
- `payments`, `payment_confirmations`
- `receipts` (token/qr)
- `attachments` (proof/confirm files)
- `audit_logs`
- `change_events`

---

## 8) Coding Conventions

### Database Access
- PDO only, **prepared statements only**
- Use transactions for multi-step operations:
  - status updates + attachments + audit log

### Controllers / Endpoints
- Use clear routing (simple PHP router or controller mapping).
- For async actions, return JSON with:
  - `ok: true/false`, `message`, `data` (optional)

### Validation & Security
- Server-side validate all inputs
- File upload hardening:
  - allowlist mime types
  - size limit
  - random filenames
  - store outside webroot if possible
- Role-based access:
  - Ticketing vs Finance vs Viewer

### Audit Logging (Required)
Log these events at minimum:
- status changes
- payment validation changes
- FP Merah lock/unlock
- document generation (invoice/report/receipt)
- any delete action (prefer soft-delete)

---

## 9) Document Generation

### Templates
- `views/pdf/invoice_internal.php`
- `views/pdf/invoice_kyai.php`
- `views/pdf/payment_report.php`
- `views/pdf/receipt.php`

### Output Storage
Write generated PDFs to:
- `storage/uploads/generated_pdfs/`

Naming convention:
- `INV-<tour_code>-internal.pdf`
- `INV-<tour_code>-kyai.pdf`
- `PAYREP-<YYYYMM>.pdf`
- `RESI-<receipt_no>.pdf`

---

## 10) Receipt Token (Encryption/Verification)

Recommended approach:
- Use HMAC (SHA-256) signature over:
  - `receipt_id|invoice_id|amount|issued_at`
- Store the signed token in DB and embed it in QR.
- Verification endpoint:
  - `/receipt/verify?token=...`

Never store raw secrets in git.

---

## 11) Backups & Ops Notes

- DB backup: daily `mysqldump`
- File backup: `storage/uploads/`
- Retention policy: define with stakeholders
- Production permissions:
  - `storage/` must be writable by web server user
- Logging:
  - app logs: `storage/logs/`
  - DB audit logs always on

---

## 12) Quick Checklist for New Contributors

1. Read: `AGENTS_Ticketing_Umroh_Haji.md`
2. Read: this file `repo_context.md`
3. Setup DB + config
4. Run locally and open:
   - Request dashboard
   - Movement dashboard
   - Payment validation (Finance role)
5. Ensure exports generate and are stored in `storage/`

---

## 13) Open Questions (Keep Updated)

- Deposit/Lunas rule variations per airline
- Token expiry policy for receipts
- Access policy for external parties (agent/kyai view-only links)
- Data retention + backup frequency
