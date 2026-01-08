# Specification: Dummy Data Generation for Payment Advice UAT

## 1. Overview
This track focuses on enhancing the existing UAT data generation script to include comprehensive and diverse dummy data for the "Payment Advice" module. This will enable business users to perform thorough User Acceptance Testing (UAT) across various outbound payment scenarios.

## 2. Functional Requirements
### 2.1 Integrate with `seed_uat_data.php`
- **Comprehensive Entry Point:** Incorporate Payment Advice data generation into the existing `database/seed_uat_data.php` script.
- **Relational Integrity:** Ensure all generated Payment Advice records are correctly linked to existing Movements created earlier in the seeding process.

### 2.2 Diverse Scenario Generation
Generate dummy data covering the following scenarios:
- **Standard Payment Advice:** Basic records for single movements.
- **Multi-Movement Advice:** (If supported by schema) Advice records spanning multiple PNRs.
- **Status Variation:** Include records with both `PENDING` and `TRANSFERRED` statuses to test dashboard indicators and filtering.
- **Carrier Diversity:** Generate records for multiple airlines (e.g., SCOOT, Saudi Arabian, Emirates) to verify carrier-specific data handling.

### 2.3 Detailed Field Population
Ensure all critical fields in the `payment_advises` table are populated with realistic test data, including:
- **Financials:** Top-up amounts, deposit percentages (20%/80% logic), and transfer totals.
- **Banking:** Accurate remitter (EEMW) and recipient (Airline) bank details.
- **Logistics:** Dates for creation, email to airline, and bank transfer.

## 3. Non-Functional Requirements
- **Idempotency:** The seeding script should be safe to run multiple times without creating duplicate records (using checks like `PNR` or `Tour Code` uniqueness).
- **Performance:** Data generation should be efficient and not significantly slow down the UAT environment setup.

## 4. Acceptance Criteria
- [ ] `database/seed_uat_data.php` includes a section for generating Payment Advice data.
- [ ] At least 10 Payment Advice records are generated with a mix of statuses and airlines.
- [ ] All generated records are correctly linked to valid Movements in the database.
- [ ] Records are visible and correctly displayed in the `public/finance/payment_advise_list.php` dashboard.

## 5. Out of Scope
- Modification of the `PaymentAdvise` model or database schema.
- Generation of actual PDF files (only the database records are seeded).
