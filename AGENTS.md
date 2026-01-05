# Digitalisasi Ticketing Umroh & Haji — Agent Guide (AGENTS.md)

This document is a **simple, open format** for guiding coding agents working on the project:
**Digitalisasi Proses Ticketing Umroh & Haji (Web-Based + FullView Display)**.

> Scope source: internal proposal text (request → flight/seat → PNR → movement → invoice → payment → report → encrypted receipt).

---

## 1) Product Summary

### Background
The current Umroh/Haji ticketing process spans multiple steps (request intake, airline checking, block seat, PNR, daily movement, invoicing, payment, reporting, receipt) and is handled via **separate spreadsheets/files** and **fragmented communication (WA/email)**. This causes inconsistent status, duplication, and slow field access—especially when changes occur (extra flight/reschedule/cancel).

### Goal
Build a **single web-based system** that centralizes the full workflow and is easy to access from:
- **Smartphone** (quick actions, condensed view)
- **Desktop/Laptop** (detailed tables, filtering, exporting)
- Optional: **monitor/TV view** for live movement dashboards

---

## 2) Core Problems to Solve
- Data scattered across sheets → **version drift**, **duplication**, **wrong status**
- Status tracking not real-time → slow decisions and higher error risk
- Communication not linked to transactions → weak audit trail
- Payment status (deposit/lunas) & **“FP Merah”** confirmation can be out of sync
- Field access is not practical on mobile
- Document security not standardized (encrypted digital receipt is still a concept)

---

## 3) MVP Deliverables (Must Have)

### Modules
1. **Request Management (Group & Individual/FID)**
   - Fields: schedule (date range), pax count, duration, requester/agent, airline/routing preferences, notes.
2. **Flight & Seat Handling**
   - Status path: `NEW → QUOTED → BLOCKED → PNR_ISSUED`
3. **Daily / Group Movement (Stage 3)**
   - Dashboard + filters (agent/airline/period), daily updates, immutable change log.
4. **Invoice & Tour Code**
   - Generate **Tour Code** and **Invoice No**
   - Output templates: **Internal Invoice** and **Invoice for Kyai/Requester**
5. **Payment & FP Merah Validation**
   - Upload proof of payment + record airline confirmation
   - Lock **FP Merah** only when conditions are met
6. **Payment Report (Stage 4)**
   - Auto recap deposit/lunas/outstanding + export Excel/PDF
7. **Encrypted Digital Receipt (Stage 5)**
   - Receipt with unique token/QR for verification (anti-tamper)

### Outputs
- Role-based web app: **Ticketing Admin**, **Finance**, **Viewer/Monitor**
- Dashboards: Request, Movement, Payment, Reports, Receipts
- Audit log (who changed what & when)
- Exports: Excel/PDF for invoice/report/receipt

---

## 4) Workflow & Status Model

### Booking / Transaction Status (Suggested)
- `NEW`: request captured
- `QUOTED`: airline options & pricing prepared
- `BLOCKED`: seats blocked with airline
- `PNR_ISSUED`: PNR/booking code available
- `IN_MOVEMENT`: stage 3 monitoring active
- `INVOICED`: tour code + invoice issued
- `PAID_DEPOSIT` / `PAID_FULL`: finance recorded payment
- `CONFIRMED_FP_MERAH`: airline confirmed + payment verified
- `REPORTED`: payment report finalized
- `RECEIPTED`: encrypted receipt issued
- `CHANGED` / `CANCELED`: change/cancel handling completed

### Change Handling Rules
A change (extra flight/reschedule/cancel) must:
1. Create a **change event** (reason + timestamp + actor)
2. Update movement data + impacted documents
3. Trigger finance review (delta/refund/penalty if applicable)
4. Re-issue invoice/report/receipt if needed

---

## 5) Data Requirements (Support Needed)

### Raw / Historical Data
- At least **6–12 months** history:
  - requests, PNR, changes, invoices, payments, reports
- Existing sheet templates:
  - Master, Daily Movement, Invoice (2 types), Payment Report
- Examples of documents:
  - internal invoice, kyai invoice, proof of payment, airline confirmation

### Master Data
- airlines & common routes, deposit/lunas rules, agent/requester directory + contacts
- definition and validation rules for **FP Merah**

---

## 6) Non-Goals (for MVP)
- Hotel/package configuration (bintang 5 etc.) beyond basic notes
- Full WA/email automation (keep as attachments/logs first)
- Complex pricing engines (use manual entry with clear provenance)

---

## 7) UX Requirements (FullView Display)
- **Responsive layout**: mobile-first with a desktop-enhanced table view
- Mobile “quick actions”:
  - Add/Update PNR, Upload proof, Mark confirmation, Generate invoice/receipt
- Desktop features:
  - Advanced filters, bulk export, review queues, audit trail browsing
- Optional “Monitor mode”:
  - large-font movement dashboard, auto-refresh

---

## 8) Security & Compliance
- Role-based access control (RBAC)
- Immutable audit log for critical actions:
  - status changes, payment validation, FP Merah lock, document generation
- Secure file uploads (proof/confirmation PDFs/images)
- Receipt verification:
  - signed token or hashed payload, QR, expiry policy (configurable)
- Backups and retention policy (define with stakeholders)

---

## 9) Acceptance Criteria (Definition of Done)
- A single group transaction can go from `NEW` to `RECEIPTED`
- PNR is searchable and visible in movement dashboards
- “FP Merah” cannot be set without:
  - payment proof recorded + airline confirmation recorded
- Exports match agreed templates (Invoice Internal, Invoice Kyai, Payment Report, Receipt)
- All edits on key fields generate audit log entries
- Works smoothly on mobile and desktop

---

## 10) Suggested Timeline (MVP 4–6 Weeks)
- Week 1: requirements lock + SOP mapping + DB/UI design
- Week 2: request + flight/seat + basic status tracking
- Week 3: movement dashboard + change log + agent filter
- Week 4: invoice (2 templates) + tour code + export
- Week 5 (optional): payment validation + FP Merah + payment report
- Week 6 (optional): encrypted receipt + UAT + training + go-live

---

## 11) Engineering Notes (Implementation Hints)

### Data Model (Minimal Entities)
- `users`, `roles`, `permissions`
- `agents`, `requesters`
- `requests` (group/individual), `bookings` (PNR), `movements`
- `invoices` (internal/kyai), `payments`, `payment_confirmations`
- `receipts` (token/qr), `attachments` (proof/confirm)
- `audit_logs`, `change_events`

### Document Generation
- Server-side PDF generation (invoice/report/receipt)
- Ensure consistent numbering (tour code + invoice no)
- Persist generated PDFs for re-download and auditability

---

## 12) Working Agreement for Coding Agents
- Do not change the status model without stakeholder sign-off
- Keep spreadsheets as import sources, not the system of record
- Every change to payments/status must be traceable (audit log)
- Prefer incremental delivery: ship Week 2–4 MVP first; add Week 5–6 enhancements after acceptance

---

## 13) Open Questions (Track as Issues)
- Final rules for deposit/lunas and confirmation thresholds per airline
- Receipt encryption method & expiry requirements
- Who can view what (agent vs internal vs kyai access policy)
- Data retention period and backup frequency

---

**Owner / PIC**: _TBD_  
**Last Updated**: 2025-12-29
