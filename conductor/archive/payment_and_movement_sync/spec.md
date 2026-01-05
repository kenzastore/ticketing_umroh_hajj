# Track Spec: Payment & Movement Synchronization

## Overview
Implement a payment recording system that directly interacts with the `movements` table's status columns (`dp1_status`, `dp2_status`, `fp_status`). This ensures that financial transactions are reflected in the operational movement dashboard.

## Objectives
1.  **Refactor Payment Logic:** Update `Payment::create` to trigger status updates on the associated `movement` record via the `Invoice`.
2.  **Payment UI Update:** Update `record_payment.php` to allow selecting which "Stage" the payment is for (DP1, DP2, or Full Payment) if not automatically deducible.
3.  **Status Sync:** Ensure that when an Invoice (linked to a Movement) is paid, the corresponding columns in `movements` are updated.

## Schema Dependencies
- `payments`: Stores the transaction.
- `invoices`: Links Payment to Booking/Movement.
- `movements`: Target for status updates (`dp1_status`, `dp2_status`, `fp_status`).

## Detailed Logic
- **Partial Payment:** If payment amount matches DP1 rule (or user selects DP1), update `movements.dp1_status` = 'PAID'.
- **Full Payment:** If balance is zero, update `movements.fp_status` = 'PAID'.

## Acceptance Criteria
- [ ] Recording a payment for an invoice updates the `movements` table.
- [ ] User can specify if a payment is for DP1, DP2, or Pelunasan.
- [ ] Movement Dashboard reflects the new payment status immediately.
