# Track Plan: Fix Dashboard Tab Interactivity

## Tasks

### Phase 1: Investigation & Root Cause Analysis
- [x] **Task 1: Inspect Layout Overlaps**
    -   **Action:** Use browser dev tools (or simulate layout) to check if the `tab-content` or a neighboring `row` is overlapping the `nav-tabs`.
    -   **Action:** Verify if the `position-relative` or `z-index` of any element is interfering.
- [x] **Task 2: Verify HTML Structure**
    -   **File:** `public/admin/dashboard.php`
    -   **Action:** Confirm `data-bs-toggle="tab"` and `data-bs-target` match the IDs of the `tab-pane` elements exactly.

### Phase 2: Implementation & Fix [checkpoint: fb7e817]
- [x] **Task 3: Apply CSS/HTML Fix**
    -   **File:** `public/admin/dashboard.php`
    -   **Action:** Adjust layout spacing or z-indexing to resolve the overlap.
    -   **Action:** Ensure the `active` and `show` classes are managed correctly by Bootstrap JS.
- [x] **Task 4: Conductor - User Manual Verification 'Phase 2: Implementation & Fix' (Protocol in workflow.md)** fb7e817

### Phase 3: Final Verification
- [ ] **Task 5: Cross-Browser Check**
    -   **Action:** Manually verify tab switching across different tab counts (using seeded data).
- [ ] **Task 6: Conductor - User Manual Verification 'Phase 3: Final Verification' (Protocol in workflow.md)**
