# Specification: UAT Dummy Data Generation System

## 1. Overview
This track implements a robust, synchronized dummy data generation system producing 100 test records for User Acceptance Testing (UAT). The system will populate every module of the application—from Master Data to Audit Logs—respecting all business rules, relational constraints, and workflow states. It includes a "reset and regenerate" mechanism triggered directly from the User Manual interface.

## 2. Functional Requirements

### 2.1 Core Generation Logic (`database/seed_uat_system.php`)
- **Quantity:** Exactly 100 high-level workflows (starting from Booking Requests).
- **Wipe and Replace:** The system must purge all existing operational data (Movements, Invoices, Payments, etc.) before generation to ensure a clean state.
- **Workflow Distribution:**
    - ~25% New Booking Requests (not yet converted).
    - ~25% Active Movements (varying DP1/DP2 statuses).
    - ~25% Ticketing Done / Financial Finalization (Invoices paid, Payment Advice generated).
    - ~25% Overdue/Urgent Scenarios (Specifically designed to trigger H-3 reminders and dashboard alerts).
- **Indonesian Localization:**
    - **Names:** Use realistic Indonesian person names (e.g., "Budi Santoso", "Siti Aminah").
    - **Companies:** Use Indonesian company names (e.g., "PT Sukses Makmur", "CV Jaya Abadi").
    - **Addresses:** Use Indonesian formatting including Kota and Provinsi (e.g., "Jl. Merdeka No. 10, Gambir, Jakarta Pusat, DKI Jakarta").
    - **Phone Numbers:** Use +62 or 08 prefix formats.
    - **Emails:** Use realistic domains (e.g., @gmail.com, @mail.id, @co.id).
    - **IDs:** Generate valid-looking NIK (16 digits) and NPWP where applicable.
    - **Terminology:** Use Indonesian business terms (e.g., "Manajer Operasional", "Staf Keuangan").
- **Master Data:** Randomly distribute records across 10+ Agents and 5+ Corporates to enable filtering tests.

### 2.2 Relational Synchronization
- **Request -> Movement:** Ensure converted records have matching passenger counts, sectors, and agent IDs.
- **Movement -> Financials:** Invoices must match the `selling_fare` defined in the movement. Payment Report lines (Sales/Cost) must be generated for all active movements.
- **Audit Logs:** Every generated record must have a chronological trail of at least 3-5 log entries (e.g., CREATE -> CONVERT -> UPDATE STATUS).

### 2.3 Role-Based Access Scenarios
- Data should be tagged or linked in a way that allows testing of:
    - **Agent Filtering:** Ensure users can only see data belonging to their agent (if applicable).
    - **Module Restrictions:** Finance users see pricing/payment data; Operational users see movement/leg data.
    - **Dashboards:** KPIs must reflect the 100-record distribution accurately.

### 2.4 Regeneration Trigger (UI)
- **Placement:** Embed a "Reset UAT Data" link/button within the `public/shared/manual.php` sidebar or a dedicated "UAT" section in the manual.
- **Mechanism:** The link will call a backend process (`public/admin/generate_uat_data.php`) that executes the CLI seeder and redirects back with a success message.
- **Security:** Access restricted to 'admin' role only.

## 3. Non-Functional Requirements
- **Performance:** Generation should complete within 30 seconds.
- **Reliability:** Use database transactions to prevent partial data states if generation fails.

## 4. Acceptance Criteria
- [ ] Running the regeneration command results in exactly 100 Booking Requests (some converted).
- [ ] Every active Movement has 4 associated Flight Legs.
- [ ] At least 25 records appear in the "Urgent Deadlines" section of the dashboard.
- [ ] A "Reset UAT Data" link is visible to Admins in the User Manual.
- [ ] Clicking the link successfully wipes old data and populates new synchronized data.

## 5. Out of Scope
- Production data migration (this is for UAT/Test environments only).
- Generation of binary files (like actual uploaded PDF proofs; placeholders/links will be used instead).
