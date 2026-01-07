# Track Spec: AGENT-001 Agent Summary (Rangkuman)

## Overview
This track implements the "Rangkuman" (Summary) module, which provides a high-level overview of metrics for each agent. This includes the number of bookings, total passengers, total selling amount, total payments received, and the remaining balance.

## Functional Requirements

### 1. Agent Summary Table
- **Columns:**
    - Agent Name
    - Total PNRs (Count of movements)
    - Total Pax (Sum of passenger_count)
    - Total Selling (Sum of selling_fare * passenger_count or total_selling)
    - Total Paid (Sum of payments related to these PNRs)
    - Outstanding Balance (Total Selling - Total Paid)

### 2. Filtering
- **Date Range:** Filter summary by movement creation date or flight date.
- **Search:** Search by agent name.

### 3. Drill-down (Optional but Recommended)
- Clicking an agent name should list the specific movements/PNRs contributing to their summary.

## Technical Requirements
- **Model:** Implement aggregation methods in `app/models/Agent.php` or a new `app/models/Summary.php`.
- **Page:** `public/admin/agent_summary.php`.
- **Layout:** Bootstrap 5 table with summary cards at the top (Total Revenue, Total Paid, Total Outstanding).

## Acceptance Criteria
- [ ] The "Rangkuman" page displays a summary table grouped by agent.
- [ ] Financial calculations (Selling, Paid, Balance) are accurate based on database records.
- [ ] The page is accessible via the "Rangkuman" card on the Dashboard.
- [ ] Export to Excel functionality is available for the summary table.
