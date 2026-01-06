# Track Spec: FINANCE-002 Payment Advise Implementation

## Overview
This track implements the "Payment Advise" module, which is used to document and track top-up payments made to airlines (e.g., Scoot) and the corresponding bank transfers. It aligns with the layout and fields in `PAYMENT_ADVISE.pdf`.

## Functional Requirements

### 1. Data Capture
- **Header Info:** Agent Name, Tour Code, Date Created, Date E-mail to Airline, Email Confirmation Status.
- **Flight/Seat Info:** PNR, Depart Date, Total Seats Confirmed, Total Seats Used (%).
- **Financial Info:** Approved Fare, Total Amount, Deposit Amount (20%), Balance Payment Amount (80%), Top Up Amount, Transfer Amount, Reference Number.
- **Bank Info (Recipient):** Company Name, Account No, Bank Name, Address.
- **Bank Info (Remitter):** Name, Account, Bank Name.
- **Transaction Info:** Date of Top Up Created, Remarks on Top Up, Date Bank Transferred, Remarks on Bank Transfer.

### 2. Layout & Display
- **Reference:** `PAYMENT_ADVISE.pdf`.
- **UI:** A detailed view that mirrors the PDF document for printing or digital reference.
- **Dashboard:** A list view to manage and filter payment advises by Agent, PNR, or Date.

### 3. Logic
- **Calculations:** Automatically calculate Balance Payment (80%) and Total Amount based on Seat counts and Fares.
- **Association:** Link Payment Advises to existing Movements/PNRs.

## Technical Requirements
- **Database:** Create `payment_advises` table.
- **Model:** `app/models/PaymentAdvise.php`.
- **Pages:**
    - `public/finance/payment_advise_list.php`
    - `public/finance/payment_advise_detail.php`
    - `public/finance/create_payment_advise.php`

## Acceptance Criteria
- [ ] Database schema includes all necessary fields for Payment Advise.
- [ ] Users can create a Payment Advise by selecting a Movement/PNR.
- [ ] The Detail View accurately mirrors the PDF reference.
- [ ] Calculations for Total Amount and Balance are accurate.
- [ ] Payment advises are listed and searchable in the Finance section.
