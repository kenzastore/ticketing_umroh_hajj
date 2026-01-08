# Track Plan: Repair and Enhance Dashboard KPI Cards

## Tasks

### Phase 1: Backend Enhancements [checkpoint: 7bdff4c]
- [x] **Task 1: Write TDD Tests for Deadline Retrieval** f6c3e86
    -   **File:** `tests/MovementDeadlineTest.php` (New)
    -   **Action:** Write tests for retrieving Ticketing, DP1, DP2, and FP deadlines.
    -   **Criteria:** Verify inclusion of past-due items and correct sorting.
- [x] **Task 2: Update Movement Model** f6c3e86
    -   **File:** `app/models/Movement.php`
    -   **Action:** Refactor `getUpcomingDeadlines` (or add specific methods) to support the four categories and past-due logic.
- [x] **Task 3: Conductor - User Manual Verification 'Phase 1: Backend Enhancements' (Protocol in workflow.md)** 7bdff4c

### Phase 2: Dashboard UI Refactor
- [ ] **Task 4: Implement Tabbed UI Structure**
    -   **File:** `public/admin/dashboard.php`
    -   **Action:** Replace the current static list with Bootstrap 5 Navigation Tabs.
- [ ] **Task 5: Implement Category Data Binding**
    -   **File:** `public/admin/dashboard.php`
    -   **Action:** Populate each tab (Ticketing, DP1, DP2, FP) with its respective data from the model.
- [ ] **Task 6: Visual Highlighting and Interactivity**
    -   **File:** `public/admin/dashboard.php`
    -   **Action:** Add CSS classes for past-due items and wrap rows in anchor tags linking to `edit_movement.php`.
- [ ] **Task 7: Conductor - User Manual Verification 'Phase 2: Dashboard UI Refactor' (Protocol in workflow.md)**

### Phase 3: Final Integration & Cleanup
- [ ] **Task 8: End-to-End Verification**
    -   **Action:** Seed test data with various deadline dates and verify the dashboard behavior across all tabs.
- [ ] **Task 9: Conductor - User Manual Verification 'Phase 3: Final Integration & Cleanup' (Protocol in workflow.md)**
