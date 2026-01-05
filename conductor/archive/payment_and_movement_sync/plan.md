# Track Plan: Payment & Movement Synchronization

This plan details the steps to synchronize payments with movement status.

## Phase 1: Model & Logic Updates
1.  **Update `Payment.php` Model:**
    - Modify `updateInvoiceStatus` to also fetch the `movement_id` via the Invoice.
    - Implement logic to determine which status column (`dp1_status`, `dp2_status`, `fp_status`) to update.
    - *Logic:* 
        - If Total Paid > 0 AND < 50%, mark DP1 PAID.
        - If Total Paid > 50% AND < 100%, mark DP2 PAID.
        - If Total Paid == 100%, mark FP PAID.
        - *Alternative:* Allow explicit "Stage" selection in the UI. Let's go with **Explicit Selection** for accuracy, as amounts vary.

2.  **Update `Invoice.php` Model:**
    - Ensure `readById` returns the linked `movement_id` (via `pnr` or `booking_id`). Note: The schema links Invoice to Booking, but we replaced Booking with Movement. We need to verify the link.
    - *Correction:* `invoices` table has `pnr` and `tour_code`. We can link to `movements` via `pnr`.

## Phase 2: UI Updates
1.  **Update `record_payment.php`:**
    - Add a dropdown for "Payment Stage": `DP1`, `DP2`, `Full Payment`, `Other`.
    - Pass this stage to the controller/model.

## Steps
- [x] Verify `Invoice` to `Movement` relationship logic.
- [x] Update `Payment::create` to accept `payment_stage`.
- [x] Implement `Payment::updateMovementStatus`.
- [x] Update `public/finance/invoice_detail.php` (UI for recording payment).
