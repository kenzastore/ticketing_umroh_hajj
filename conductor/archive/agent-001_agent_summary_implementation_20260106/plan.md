# Track Plan: AGENT-001 Agent Summary (Rangkuman)

## Tasks

### Phase 1: Logic & Data
- [x] **Task 1: Implement Aggregation Logic**
    -   **File:** `app/models/Agent.php`
    -   **Action:** Add `getAgentSummary()` method. This method should perform a SQL query with `GROUP BY agent_name` and aggregate financial data from `movements` and potentially `payments`/`invoices`.

### Phase 2: UI Implementation
- [~] **Task 2: Create Agent Summary Page**
    -   **File:** `public/admin/agent_summary.php`
    -   **Action:** Implement the summary table and KPI cards.
    -   **Tech:** PHP, Bootstrap 5.

- [x] **Task 3: Implement Excel Export**
    -   **File:** `public/admin/export_agent_summary.php`
    -   **Action:** Add export functionality for the summary table.

### Phase 3: Integration
- [x] **Task 4: Update Dashboard Link**
    -   **File:** `public/admin/dashboard.php`
    -   **Action:** Link the "Rangkuman" card to `agent_summary.php`.

- [x] **Task 5: Final Verification**
    -   **Action:** Verify calculations with seeded data.
